<?php

namespace App\Http\Controllers;

use App\Support\ExcelImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class PersonnelImportController extends Controller
{
    private const SESSION_KEY = 'personnel_excel_import';

    /**
     * Show the upload form and the usage guide.
     */
    public function show(): View
    {
        $this->authorizeSuperAdmin();

        return view('settings.import.index');
    }

    /**
     * Download a UTF-8 CSV template for Excel.
     */
    public function template(Request $request): Response
    {
        $this->authorizeSuperAdmin();

        $mode = $request->query('mode') === 'scores' ? 'scores' : 'personnel';
        $filename = 'personnel-import-'.$mode.'.csv';

        return response((new ExcelImport)->template($mode), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Parse the uploaded file and show a preview before anything is saved.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120', 'extensions:csv,txt,xls,xlsx,xml'],
            'mode' => ['required', Rule::in(['auto', 'personnel', 'scores'])],
        ]);

        try {
            $payload = (new ExcelImport)->parse($request->file('file'), $validated['mode']);
        } catch (RuntimeException $exception) {
            return redirect()->route('settings.import.index')
                ->withInput()
                ->with('error', 'فایل قابل پردازش نیست: '.$exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('settings.import.index')
                ->withInput()
                ->with('error', 'خطای غیرمنتظرهای هنگام خواندن فایل رخ داد. لطفاً فایل را بررسی کنید.');
        }

        $token = bin2hex(random_bytes(24));
        $payload['token'] = $token;

        $request->session()->put(self::SESSION_KEY, $payload);

        return view('settings.import.preview', ['import' => $payload]);
    }

    /**
     * Confirm the previewed import and write it to the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'token' => ['required', 'string'],
        ]);

        $payload = $request->session()->get(self::SESSION_KEY);

        if (! is_array($payload) || ($payload['token'] ?? null) !== $validated['token']) {
            return redirect()->route('settings.import.index')
                ->with('error', 'نشست وارد کردن منقضی شده است. لطفاً فایل را دوباره بارگذاری کنید.');
        }

        if (($payload['summary']['errors'] ?? 0) > 0) {
            return redirect()->route('settings.import.index')
                ->with('error', 'فایل دارای ردیفهای نادرست است. ابتدا خطاها را برطرف کنید.');
        }

        try {
            $counts = DB::transaction(fn (): array => (new ExcelImport)->commit($payload, (int) auth()->id()));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('settings.import.index')
                ->with('error', 'در هنگام ذخیره اطلاعات خطایی رخ داد. هیچ تغییری ذخیره نشد.');
        }

        $request->session()->forget(self::SESSION_KEY);

        $message = 'وارد کردن پرسنل با موفقیت انجام شد: '
            .fa_num($counts['personnels_created']).' پرسنل جدید، '
            .fa_num($counts['evaluations_created']).' ارزیابی ثبت شد';

        if ($counts['existing_used'] > 0) {
            $message .= ' و '.fa_num($counts['existing_used']).' پرسنل موجود بهروزرسانی شدند';
        }

        return redirect()->route('settings.import.index')->with('success', $message.'.');
    }

    private function authorizeSuperAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('super-admin'), 403);
    }
}
