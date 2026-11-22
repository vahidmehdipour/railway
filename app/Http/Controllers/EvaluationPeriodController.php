<?php

namespace App\Http\Controllers;

use App\Models\EvaluationPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationPeriodController extends Controller
{
    /**
     * Display the evaluation periods.
     */
    public function index(): View
    {
        return view('periods.index', [
            'periods' => EvaluationPeriod::query()
                ->withCount('evaluations')
                ->orderByDesc('start_date')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new period.
     */
    public function create(): View
    {
        return view('periods.create');
    }

    /**
     * Store a newly created period.
     */
    public function store(Request $request): RedirectResponse
    {
        EvaluationPeriod::create($this->validated($request));

        return redirect()
            ->route('periods.index')
            ->with('success', 'دوره ارزیابی جدید ثبت شد.');
    }

    /**
     * Show the form for editing the specified period.
     */
    public function edit(EvaluationPeriod $period): View
    {
        return view('periods.edit', compact('period'));
    }

    /**
     * Update the specified period.
     */
    public function update(Request $request, EvaluationPeriod $period): RedirectResponse
    {
        $period->update($this->validated($request));

        return redirect()
            ->route('periods.index')
            ->with('success', 'دوره ارزیابی به‌روزرسانی شد.');
    }

    /**
     * Remove the specified period.
     */
    public function destroy(EvaluationPeriod $period): RedirectResponse
    {
        if ($period->evaluations()->exists()) {
            return redirect()
                ->route('periods.index')
                ->with('error', 'این دوره دارای ارزیابی ثبت‌شده است و قابل حذف نیست.');
        }

        $period->delete();

        return redirect()
            ->route('periods.index')
            ->with('success', 'دوره ارزیابی حذف شد.');
    }

    /**
     * Validate the period request.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
