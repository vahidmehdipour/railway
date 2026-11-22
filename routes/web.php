<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EvaluationPeriodController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Support\Permissions;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:'.Permissions::VIEW_DASHBOARD)
        ->name('dashboard');

    // Personnel
    Route::middleware('permission:'.Permissions::CREATE_PERSONNELS)->group(function () {
        Route::get('personnels/create', [PersonnelController::class, 'create'])->name('personnels.create');
        Route::post('personnels', [PersonnelController::class, 'store'])->name('personnels.store');
    });

    Route::middleware('permission:'.Permissions::VIEW_PERSONNELS)->group(function () {
        Route::get('personnels/export', [PersonnelController::class, 'export'])->name('personnels.export');
        Route::get('personnels/export-all/pdf', [PersonnelController::class, 'exportAllPdf'])->name('personnels.export-all-pdf');
        Route::get('personnels/{personnel}/pdf', [PersonnelController::class, 'exportPdf'])->name('personnels.pdf');
        Route::get('personnels', [PersonnelController::class, 'index'])->name('personnels.index');
        Route::get('personnels/{personnel}', [PersonnelController::class, 'show'])->name('personnels.show');
    });

    Route::middleware('permission:'.Permissions::EDIT_PERSONNELS)->group(function () {
        Route::get('personnels/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnels.edit');
        Route::put('personnels/{personnel}', [PersonnelController::class, 'update'])->name('personnels.update');
    });

    Route::middleware('permission:'.Permissions::DELETE_PERSONNELS)->group(function () {
        Route::delete('personnels/{personnel}', [PersonnelController::class, 'destroy'])->name('personnels.destroy');
    });

    // Evaluations
    Route::middleware('permission:'.Permissions::CREATE_EVALUATIONS)->group(function () {
        Route::get('evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('evaluations', [EvaluationController::class, 'store'])->name('evaluations.store');
    });

    Route::middleware('permission:'.Permissions::VIEW_EVALUATIONS)->group(function () {
        Route::get('evaluations/export', [EvaluationController::class, 'export'])->name('evaluations.export');
        Route::get('evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    });

    Route::middleware('permission:'.Permissions::EDIT_EVALUATIONS)->group(function () {
        Route::get('evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])->name('evaluations.edit');
        Route::put('evaluations/{evaluation}', [EvaluationController::class, 'update'])->name('evaluations.update');
    });

    Route::middleware('permission:'.Permissions::DELETE_EVALUATIONS)->group(function () {
        Route::delete('evaluations/{evaluation}', [EvaluationController::class, 'destroy'])->name('evaluations.destroy');
    });

    // Evaluation periods
    Route::middleware('permission:'.Permissions::CREATE_PERIODS)->group(function () {
        Route::get('periods/create', [EvaluationPeriodController::class, 'create'])->name('periods.create');
        Route::post('periods', [EvaluationPeriodController::class, 'store'])->name('periods.store');
    });

    Route::middleware('permission:'.Permissions::VIEW_PERIODS)->group(function () {
        Route::get('periods', [EvaluationPeriodController::class, 'index'])->name('periods.index');
    });

    Route::middleware('permission:'.Permissions::EDIT_PERIODS)->group(function () {
        Route::get('periods/{period}/edit', [EvaluationPeriodController::class, 'edit'])->name('periods.edit');
        Route::put('periods/{period}', [EvaluationPeriodController::class, 'update'])->name('periods.update');
    });

    Route::middleware('permission:'.Permissions::DELETE_PERIODS)->group(function () {
        Route::delete('periods/{period}', [EvaluationPeriodController::class, 'destroy'])->name('periods.destroy');
    });

    Route::middleware(['admin', 'permission:'.Permissions::MANAGE_SETTINGS])->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/stations', [SettingController::class, 'storeStation'])->name('settings.stations.store');
        Route::get('settings/stations/{station}/edit', [SettingController::class, 'editStation'])->name('settings.stations.edit');
        Route::put('settings/stations/{station}', [SettingController::class, 'updateStation'])->name('settings.stations.update');
        Route::delete('settings/stations/{station}', [SettingController::class, 'destroyStation'])->name('settings.stations.destroy');
        Route::post('settings/roles', [SettingController::class, 'storeRole'])->name('settings.roles.store');
        Route::get('settings/roles/{role}/edit', [SettingController::class, 'editRole'])->name('settings.roles.edit');
        Route::put('settings/roles/{role}', [SettingController::class, 'updateRole'])->name('settings.roles.update');
        Route::delete('settings/roles/{role}', [SettingController::class, 'destroyRole'])->name('settings.roles.destroy');
    });

    Route::middleware(['admin', 'permission:'.Permissions::MANAGE_SETTINGS])->group(function () {
        Route::get('appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');
        Route::put('appearance', [AppearanceController::class, 'update'])->name('appearance.update');
    });

    Route::middleware(['admin', 'permission:'.Permissions::VIEW_ACTIVITY_LOG])->group(function () {
        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    Route::resource('users', UserController::class)->middleware('role:super-admin');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
