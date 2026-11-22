<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Personnel;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index(): View
    {
        $totalPersonnel = Personnel::count();
        $activePersonnel = Personnel::where('is_active', true)->count();
        $totalEvaluations = Evaluation::count();
        $totalPeriods = EvaluationPeriod::count();

        $topPersonnel = Personnel::query()
            ->with('role')
            ->withCount('evaluations')
            ->get()
            ->sortByDesc(fn (Personnel $personnel) => $personnel->final_score)
            ->take(5)
            ->values();

        $recentEvaluations = Evaluation::query()
            ->with(['personnel.role', 'period', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        $activePeriods = EvaluationPeriod::query()
            ->active()
            ->orderByDesc('start_date')
            ->get();

        return view('dashboard', compact(
            'totalPersonnel',
            'activePersonnel',
            'totalEvaluations',
            'totalPeriods',
            'topPersonnel',
            'recentEvaluations',
            'activePeriods',
        ));
    }
}
