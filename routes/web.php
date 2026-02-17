<?php

use App\Http\Controllers\Admin\UnitStatusController;
use App\Livewire\AdminCategories;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminEmployees;
use App\Livewire\AdminLogs;
use App\Livewire\AdminUnitForm;
use App\Livewire\AdminUnitQrAction;
use App\Livewire\AdminUnitsIndex;
use App\Livewire\PublicShowroom;
use App\Livewire\UnitDetail;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicShowroom::class)->name('home');
Route::get('/units/{unit}', UnitDetail::class)->name('units.show');

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/units/{unit}/qr', AdminUnitQrAction::class)
            ->middleware(['staff', 'signed', 'can:viewQr,unit'])
            ->name('units.qr');
        Route::post('/units/{unit}/set-sold', [UnitStatusController::class, 'setSold'])
            ->middleware(['staff', 'can:changeStatus,unit', 'throttle:20,1'])
            ->name('units.set-sold');
        Route::post('/units/{unit}/set-available', [UnitStatusController::class, 'setAvailable'])
            ->middleware(['staff', 'can:changeStatus,unit', 'throttle:20,1'])
            ->name('units.set-available');
    });

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/units', AdminUnitsIndex::class)
            ->middleware('can:viewAny,'.Unit::class)
            ->name('units.index');
        Route::get('/units/create', AdminUnitForm::class)
            ->middleware('can:create,'.Unit::class)
            ->name('units.create');
        Route::get('/units/{unit}/edit', AdminUnitForm::class)
            ->middleware('can:update,unit')
            ->name('units.edit');
        Route::get('/categories', AdminCategories::class)->name('categories.index');
        Route::get('/employees', AdminEmployees::class)->name('employees.index');
        Route::get('/logs', AdminLogs::class)
            ->middleware('can:viewAny,'.UnitStatusLog::class)
            ->name('logs.index');
    });

Route::get('dashboard', function () {
    $user = auth()->user();

    if ((bool) ($user?->is_admin ?? false)) {
        return redirect()->route('admin.dashboard');
    }

    $canViewInventory = (bool) ($user?->is_admin ?? false);

    if (! $canViewInventory) {
        return view('dashboard', [
            'canViewInventory' => false,
            'searchQuery' => '',
            'totalUnits' => 0,
            'availableUnits' => 0,
            'soldUnits' => 0,
            'addedThisWeek' => 0,
            'recentLogs' => collect(),
        ]);
    }

    $searchQuery = trim((string) request()->query('q', ''));

    $unitsQuery = Unit::query()
        ->when(
            $searchQuery !== '',
            fn ($query) => $query->where('name', 'like', '%'.$searchQuery.'%'),
        );

    $totalUnits = (clone $unitsQuery)->count();
    $availableUnits = (clone $unitsQuery)->where('status', Unit::STATUS_AVAILABLE)->count();
    $soldUnits = (clone $unitsQuery)->where('status', Unit::STATUS_SOLD)->count();
    $addedThisWeek = (clone $unitsQuery)->where('created_at', '>=', now()->startOfWeek())->count();

    $recentLogs = UnitStatusLog::query()
        ->with(['unit', 'user'])
        ->when(
            $searchQuery !== '',
            fn ($query) => $query->whereHas(
                'unit',
                fn ($unitQuery) => $unitQuery->where('name', 'like', '%'.$searchQuery.'%'),
            ),
        )
        ->latest()
        ->limit(10)
        ->get();

    return view('dashboard', [
        'canViewInventory' => true,
        'searchQuery' => $searchQuery,
        'totalUnits' => $totalUnits,
        'availableUnits' => $availableUnits,
        'soldUnits' => $soldUnits,
        'addedThisWeek' => $addedThisWeek,
        'recentLogs' => $recentLogs,
    ]);
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
