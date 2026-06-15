<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Livewire\AdminCategories;
use App\Livewire\AdminDashboard;
use App\Livewire\AdminEmployees;
use App\Livewire\AdminInquiriesIndex;
use App\Livewire\AdminLogs;
use App\Livewire\AdminShopSettings;
use App\Livewire\AdminUnitForm;
use App\Livewire\AdminUnitQrAction;
use App\Livewire\AdminUnitsIndex;
use App\Livewire\Public\VehicleComparison;
use App\Livewire\PublicShowroom;
use App\Livewire\UnitDetail;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use Illuminate\Support\Facades\Route;

/**
 * Public Routes
 */
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/garage', \App\Livewire\Public\MyGarage::class)->name('garage');
});

use App\Livewire\Public\AuctionHall;
use App\Livewire\Public\AuctionRoom;

Route::get('/', PublicShowroom::class)->name('home');
Route::get('/units/{unit}', UnitDetail::class)->name('units.show');
Route::get('/comparison', VehicleComparison::class)->name('comparison');
Route::get('/auction', AuctionHall::class)->name('auction.hall');
Route::get('/auction/{auction}', AuctionRoom::class)->name('auction.room');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/sitemap.xml', function () {
    $units = \App\Models\Unit::query()->where('status', \App\Models\Unit::STATUS_AVAILABLE)->latest()->get();
    $content = view('pages.sitemap', compact('units'))->render();

    return response($content, 200)
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

/**
 * Admin & Staff Routes
 */
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/messages', \App\Livewire\AdminMessagesIndex::class)
            ->name('messages');

        // Inventory Management (Shared by Staff/Admin)
        Route::get('/units', AdminUnitsIndex::class)
            ->middleware('can:viewAny,'.Unit::class)
            ->name('units.index');
        Route::get('/units/create', AdminUnitForm::class)
            ->middleware('can:create,'.Unit::class)
            ->name('units.create');
        Route::get('/units/{unit}/edit', AdminUnitForm::class)
            ->middleware('can:update,unit')
            ->name('units.edit');
        Route::get('/units/{unit}/qr', AdminUnitQrAction::class)
            ->middleware(['staff', 'signed', 'can:viewQr,unit'])
            ->name('units.qr');

        // Management & Analytics (Admin Only)
        Route::middleware(['admin'])->group(function (): void {
            Route::get('/', AdminDashboard::class)->name('dashboard');
            Route::get('/categories', AdminCategories::class)->name('categories.index');
            Route::get('/employees', AdminEmployees::class)->name('employees.index');
            Route::get('/inquiries', AdminInquiriesIndex::class)->name('inquiries.index');
            Route::get('/auctions', \App\Livewire\AdminAuctionsIndex::class)->name('auctions.index');
            Route::get('/auctions/create', \App\Livewire\AdminAuctionForm::class)->name('auctions.create');
            Route::get('/auctions/{auction}/edit', \App\Livewire\AdminAuctionForm::class)->name('auctions.edit');
            Route::post('/auctions/{auction}/activate', [\App\Http\Controllers\AuctionController::class, 'activate'])->name('auctions.activate');

            Route::get('/deposits', \App\Livewire\AdminDepositVerification::class)->name('deposits.index');

            Route::get('/logs', AdminLogs::class)
                ->middleware('can:viewAny,'.UnitStatusLog::class)
                ->name('logs.index');
            Route::get('/customization', \App\Livewire\AdminCustomization::class)->name('customization');
            Route::get('/settings/shop', AdminShopSettings::class)->name('settings.shop');
        });
    });

/**
 * Unified Dashboard Redirector
 */
Route::get('dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    if ((bool) ($user?->is_admin ?? false)) {
        return redirect()->route('admin.dashboard');
    }

    if ((bool) ($user?->isStaff() ?? false)) {
        return redirect()->route('admin.units.index');
    }

    // Default portal for verified collectors
    return redirect()->route('garage');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
