<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvaluationRequest;
use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Personnel;
use App\Models\Station;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluationController extends Controller
{
    /**
     * Display a filtered listing of the evaluations.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['period_id', 'personnel_id', 'search']);

        $evaluations = $this->filteredQuery($filters)
            ->paginate(10)
            ->withQueryString();

        $totals = $this->filteredQuery($filters)
            ->get()
            ->map(fn (Evaluation $evaluation) => $evaluation->calculatedTotal());

        $stats = (object) [
            'evaluations_count' => $totals->count(),
            'average_score' => $totals->avg() ?? 0,
            'highest_score' => $totals->max() ?? 0,
            'lowest_score' => $totals->min() ?? 0,
        ];

        $scoringSettings = $this->scoringSettings();
        $maxTotal = $scoringSettings['max_theory_score']
            + $scoringSettings['max_practical_score']
            + $scoringSettings['max_ethics_score'];

        return view('evaluations.index', [
            'evaluations' => $evaluations,
            'periods' => EvaluationPeriod::orderByDesc('start_date')->get(),
            'personnels' => Personnel::orderBy('last_name')->get(),
            'filters' => $filters,
            'stats' => $stats,
            'maxTotal' => $maxTotal,
        ]);
    }

    /**
     * Show the form for creating a new evaluation.
     */
    public function create(Request $request): View
    {
        return view('evaluations.create', [
            'personnels' => Personnel::query()
                ->with('role')
                ->orderBy('last_name')
                ->get(),
            'periods' => EvaluationPeriod::query()
                ->active()
                ->orderByDesc('start_date')
                ->get(),
            'stations' => Station::orderBy('name')->get(),
            'scoringSettings' => $this->scoringSettings(),
            'selectedPersonnel' => $request->integer('personnel_id') ?: null,
            'selectedPeriod' => $request->integer('period_id') ?: null,
        ]);
    }

    /**
     * Store a newly created evaluation.
     */
    public function store(StoreEvaluationRequest $request): RedirectResponse
    {
        Evaluation::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'ارزیابی با موفقیت ثبت شد.');
    }

    /**
     * Show the form for editing the specified evaluation.
     */
    public function edit(Evaluation $evaluation): View
    {
        return view('evaluations.edit', [
            'evaluation' => $evaluation,
            'personnels' => Personnel::query()
                ->with('role')
                ->orderBy('last_name')
                ->get(),
            'periods' => EvaluationPeriod::orderByDesc('start_date')->get(),
            'stations' => Station::orderBy('name')->get(),
            'scoringSettings' => $this->scoringSettings(),
        ]);
    }

    /**
     * Update the specified evaluation.
     */
    public function update(StoreEvaluationRequest $request, Evaluation $evaluation): RedirectResponse
    {
        $evaluation->update($request->validated());

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'ارزیابی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy(Evaluation $evaluation): RedirectResponse
    {
        $evaluation->delete();

        return redirect()
            ->route('evaluations.index')
            ->with('success', 'ارزیابی مورد نظر حذف شد.');
    }

    /**
     * Export the filtered evaluations as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $request->only(['period_id', 'personnel_id', 'search']);

        $evaluations = $this->filteredQuery($filters)->get();

        return response()->streamDownload(function () use ($evaluations): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'ردیف',
                'نام پرسنل',
                'نقش',
                'دوره ارزیابی',
                'نمره تئوری',
                'نمره عملی',
                'نمره اخلاق',
                'سانحهها',
                'شبه سانحهها',
                'اخطارها',
                'نمره کل',
                'ثبت‌کننده',
                'تاریخ ثبت',
            ]);

            foreach ($evaluations as $index => $evaluation) {
                fputcsv($handle, [
                    $index + 1,
                    $evaluation->personnel?->full_name,
                    $evaluation->personnel?->role?->name_fa,
                    $evaluation->period?->title,
                    $evaluation->theory_score,
                    $evaluation->practical_score,
                    $evaluation->ethics_score,
                    $evaluation->accident_count,
                    $evaluation->near_miss_count,
                    $evaluation->warning_count,
                    $evaluation->total_score,
                    $evaluation->user?->name,
                    $evaluation->created_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'evaluations-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Get the scoring limits and penalty coefficients from the settings table.
     *
     * @return array<string, float>
     */
    private function scoringSettings(): array
    {
        return [
            'max_theory_score' => (float) get_setting('max_theory_score', 14),
            'max_practical_score' => (float) get_setting('max_practical_score', 4),
            'max_ethics_score' => (float) get_setting('max_ethics_score', 2),
            'accident_coefficient' => (float) get_setting('accident_coefficient', 2),
            'near_miss_coefficient' => (float) get_setting('near_miss_coefficient', 1),
            'warning_coefficient' => (float) get_setting('warning_coefficient', 0.5),
        ];
    }

    /**
     * Build the filtered evaluation query shared by the index and export actions.
     *
     * @param  array<string, mixed>  $filters
     * @return Builder<Evaluation>
     */
    private function filteredQuery(array $filters): Builder
    {
        return Evaluation::query()
            ->with(['personnel.role', 'period', 'user'])
            ->when($filters['period_id'] ?? null, fn ($query, $periodId) => $query->where('period_id', $periodId))
            ->when($filters['personnel_id'] ?? null, fn ($query, $personnelId) => $query->where('personnel_id', $personnelId))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->whereHas('personnel', function ($query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('national_code', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at');
    }
}
