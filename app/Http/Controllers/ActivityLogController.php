<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(): View
    {
        $activities = Activity::query()
            ->with('causer')
            ->latest()
            ->paginate(20);

        return view('activity-log.index', compact('activities'));
    }
}
