<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonnelRequest;
use App\Http\Requests\UpdatePersonnelRequest;
use App\Models\Personnel;
use App\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonnelController extends Controller
{
    /** @var list<string> */
    private const SORT_OPTIONS = ['name', 'score', 'entry_year', 'decree_year'];

    private const PER_PAGE = 10;

    /**
     * Display a filtered and sorted listing of the personnel.
     */
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $query = $this->filteredQuery($filters);
        $this->applySorting($query, $filters['sort'], $filters['direction']);

        $personnels = $query->paginate(self::PER_PAGE)->withQueryString();
        $roles = Role::orderBy('name_fa')->get();

        return view('personnels.index', compact('personnels', 'roles', 'filters'));
    }

    /**
     * Display the personnel profile and full evaluation history.
     */
    public function show(int $id): View
    {
        return view('personnels.show', [
            'personnel' => $this->profileQuery($id),
        ]);
    }

    /**
     * Export the personnel profile and evaluation history as a PDF.
     */
    public function exportPdf(int $id): Response
    {
        $personnel = $this->profileQuery($id);

        return Pdf::loadView('personnels.pdf', ['personnel' => $personnel])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->download('personnel-'.$personnel->id.'.pdf');
    }

    /**
     * Export all filtered personnel with their final scores as one PDF.
     */
    public function exportAllPdf(Request $request): Response
    {
        $filters = $this->filters($request);

        $query = $this->filteredQuery($filters);
        $this->applySorting($query, $filters['sort'], $filters['direction']);

        return Pdf::loadView('personnels.pdf-all', [
            'personnels' => $query->get(),
            'filters' => $filters,
        ])
            ->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->download('personnels-all-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Load a personnel with their role and complete evaluation history.
     */
    private function profileQuery(int $id): Personnel
    {
        return Personnel::query()
            ->with([
                'role',
                'evaluations' => fn ($query) => $query
                    ->with('station')
                    ->orderByDesc('evaluation_date')
                    ->orderByDesc('id'),
            ])
            ->findOrFail($id);
    }

    /**
     * Normalize the list filters and sorting inputs.
     *
     * @return array{search: string, role_id: int|null, status: string|null, sort: string, direction: string}
     */
    private function filters(Request $request): array
    {
        $sort = (string) $request->query('sort', 'name');

        if (! in_array($sort, self::SORT_OPTIONS, true)) {
            $sort = 'name';
        }

        $defaultDirection = $sort === 'name' ? 'asc' : 'desc';
        $direction = strtolower((string) $request->query('direction', $defaultDirection));
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : $defaultDirection;

        $status = (string) $request->query('status', '');
        $roleId = $request->query('role_id');

        return [
            'search' => trim((string) $request->query('search', '')),
            'role_id' => is_numeric($roleId) ? (int) $roleId : null,
            'status' => in_array($status, ['active', 'inactive'], true) ? $status : null,
            'sort' => $sort,
            'direction' => $direction,
        ];
    }

    /**
     * Build the shared filtered query, including a computed final score.
     *
     * @param array{search: string, role_id: int|null, status: string|null, sort: string, direction: string} $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        return Personnel::query()
            ->selectRaw('personnels.*, '.$this->scoreExpression().' as computed_score')
            ->with('role')
            ->withCount('evaluations')
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['search'];

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('national_code', 'like', "%{$search}%");
                });
            })
            ->when($filters['role_id'] !== null, fn (Builder $query) => $query->where('role_id', $filters['role_id']))
            ->when($filters['status'] === 'active', fn (Builder $query) => $query->where('personnels.is_active', true))
            ->when($filters['status'] === 'inactive', fn (Builder $query) => $query->where('personnels.is_active', false));
    }

    /**
     * Apply the selected sorting option to a personnel query.
     */
    private function applySorting(Builder $query, string $sort, string $direction): void
    {
        if ($sort === 'score') {
            $query->orderByRaw('computed_score '.$direction);
        } elseif (in_array($sort, ['entry_year', 'decree_year'], true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('last_name', $direction);
            $query->orderBy('first_name', $direction);
        }

        if ($sort !== 'name') {
            $query->orderBy('last_name')->orderBy('first_name');
        }
    }

    /**
     * Build the SQL expression used for final-score sorting and display.
     */
    private function scoreExpression(): string
    {
        $accident = $this->coefficient('accident_coefficient', 2);
        $nearMiss = $this->coefficient('near_miss_coefficient', 1);
        $warning = $this->coefficient('warning_coefficient', 0.5);

        return '('
            .'ROUND(COALESCE((SELECT AVG(e.theory_score) FROM evaluations e WHERE e.personnel_id = personnels.id), 0), 2)'
            .' + ROUND(COALESCE((SELECT AVG(e.practical_score) FROM evaluations e WHERE e.personnel_id = personnels.id), 0), 2)'
            .' + ROUND(COALESCE((SELECT AVG(e.ethics_score) FROM evaluations e WHERE e.personnel_id = personnels.id), 0), 2)'
            .' - (SELECT COALESCE(SUM(e.accident_count), 0) FROM evaluations e WHERE e.personnel_id = personnels.id) * '.$accident
            .' - (SELECT COALESCE(SUM(e.near_miss_count), 0) FROM evaluations e WHERE e.personnel_id = personnels.id) * '.$nearMiss
            .' - (SELECT COALESCE(SUM(e.warning_count), 0) FROM evaluations e WHERE e.personnel_id = personnels.id) * '.$warning
            .')';
    }

    private function coefficient(string $key, float $default): string
    {
        return number_format((float) get_setting($key, $default), 4, '.', '');
    }

    /**
     * Show the form for creating a new personnel.
     */
    public function create(): View
    {
        return view('personnels.create', [
            'roles' => Role::orderBy('name_fa')->get(),
        ]);
    }

    /**
     * Store a newly created personnel.
     */
    public function store(StorePersonnelRequest $request): RedirectResponse
    {
        Personnel::create($request->validated());

        return redirect()
            ->route('personnels.index')
            ->with('success', 'پرسنل جدید با موفقیت ثبت شد.');
    }

    /**
     * Show the form for editing the specified personnel.
     */
    public function edit(Personnel $personnel): View
    {
        return view('personnels.edit', [
            'personnel' => $personnel,
            'roles' => Role::orderBy('name_fa')->get(),
        ]);
    }

    /**
     * Update the specified personnel.
     */
    public function update(UpdatePersonnelRequest $request, Personnel $personnel): RedirectResponse
    {
        $personnel->update($request->validated());

        return redirect()
            ->route('personnels.index')
            ->with('success', 'اطلاعات پرسنل با موفقیت بهروزرسانی شد.');
    }

    /**
     * Remove the specified personnel.
     */
    public function destroy(Personnel $personnel): RedirectResponse
    {
        $personnel->delete();

        return redirect()
            ->route('personnels.index')
            ->with('success', 'پرسنل مورد نظر حذف شد.');
    }

    /**
     * Export the filtered personnel list as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);

        $query = $this->filteredQuery($filters);
        $this->applySorting($query, $filters['sort'], $filters['direction']);

        $personnels = $query->get();

        return response()->streamDownload(function () use ($personnels): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ردیف', 'نام', 'نام خانوادگی', 'نقش', 'سال ورود', 'سال حکم', 'کد ملی', 'وضعیت', 'امتیاز نهایی', 'تعداد ارزیابی']);

            foreach ($personnels as $index => $personnel) {
                fputcsv($handle, [
                    $index + 1,
                    $personnel->first_name,
                    $personnel->last_name,
                    $personnel->role?->name_fa,
                    $personnel->entry_year,
                    $personnel->decree_year,
                    $personnel->national_code,
                    $personnel->is_active ? 'فعال' : 'غیرفعال',
                    number_format((float) $personnel->computed_score, 2, '.', ''),
                    $personnel->evaluations_count,
                ]);
            }

            fclose($handle);
        }, 'personnels-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
