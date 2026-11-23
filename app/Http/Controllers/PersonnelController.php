<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonnelRequest;
use App\Http\Requests\UpdatePersonnelRequest;
use App\Models\Personnel;
use App\Models\Role;
use App\Support\ExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PersonnelController extends Controller
{
    /** @var list<string> */
    private const SORT_OPTIONS = ['name', 'score', 'entry_year', 'decree_year'];

    /** @var list<string> */
    private const SECONDARY_OPTIONS = ['none', 'name', 'score', 'entry_year', 'decree_year'];

    private const PER_PAGE = 10;

    /**
     * Display a filtered and sorted listing of the personnel.
     */
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $query = $this->filteredQuery($filters);
        $this->applySorting($query, $filters);

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
        $this->applySorting($query, $filters);

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
     * @return array{search: string, role_id: int|null, status: string|null, sort: string, secondary: string, direction: string}
     */
    private function filters(Request $request): array
    {
        $sort = (string) $request->query('sort', 'name');

        if (! in_array($sort, self::SORT_OPTIONS, true)) {
            $sort = 'name';
        }

        $defaultSecondary = $sort === 'score' ? 'entry_year' : 'none';
        $secondary = $request->query('secondary') === null
            ? $defaultSecondary
            : strtolower((string) $request->query('secondary'));

        if (! in_array($secondary, self::SECONDARY_OPTIONS, true) || $secondary === $sort) {
            $secondary = 'none';
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
            'secondary' => $secondary,
            'direction' => $direction,
        ];
    }

    /**
     * Build the shared filtered query, including a computed final score.
     *
     * @param  array{search: string, role_id: int|null, status: string|null, sort: string, secondary: string, direction: string}  $filters
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
     * Apply the primary and optional secondary sort to a personnel query.
     *
     * @param  array{sort: string, secondary: string, direction: string}  $filters
     */
    private function applySorting(Builder $query, array $filters): void
    {
        $this->applyOrder($query, $filters['sort'], $filters['direction']);

        if ($filters['secondary'] !== 'none') {
            $this->applyOrder($query, $filters['secondary'], $filters['direction']);
        }

        if (! in_array('name', [$filters['sort'], $filters['secondary']], true)) {
            $query->orderBy('personnels.last_name')->orderBy('personnels.first_name');
        }

        $query->orderBy('personnels.id');
    }

    private function applyOrder(Builder $query, string $key, string $direction): void
    {
        if ($key === 'name') {
            $query->orderBy('personnels.last_name', $direction)
                ->orderBy('personnels.first_name', $direction);

            return;
        }

        $query->orderByRaw($this->orderExpression($key).' '.$direction);
    }

    private function orderExpression(string $key): string
    {
        return match ($key) {
            'score' => 'computed_score',
            'entry_year' => 'personnels.entry_year',
            'decree_year' => 'personnels.decree_year',
            default => 'personnels.last_name',
        };
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
     * Export the filtered personnel list as an Excel workbook.
     */
    public function export(Request $request): Response
    {
        $filters = $this->filters($request);

        $query = $this->filteredQuery($filters);
        $this->applySorting($query, $filters);

        $rows = $query->get()->map(fn (Personnel $personnel, int $index): array => [
            $index + 1,
            $personnel->first_name,
            $personnel->last_name,
            $personnel->role?->name_fa,
            $personnel->entry_year,
            $personnel->decree_year,
            $personnel->national_code,
            $personnel->is_active ? 'فعال' : 'غیرفعال',
            round((float) $personnel->computed_score, 2),
            $personnel->evaluations_count,
        ]);

        return ExcelExport::response(
            ['ردیف', 'نام', 'نام خانوادگی', 'نقش', 'سال ورود', 'سال حکم', 'کد ملی', 'وضعیت', 'امتیاز نهایی', 'تعداد ارزیابی'],
            $rows,
            'personnels-'.now()->format('Y-m-d').'.xls',
        );
    }
}
