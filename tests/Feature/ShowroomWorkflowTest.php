<?php

use App\Livewire\AdminCategories;
use App\Livewire\AdminEmployees;
use App\Livewire\AdminUnitForm;
use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitImage;
use App\Models\UnitStatusLog;
use App\Models\User;
use App\Services\UnitStatusService;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('public access works', function (): void {
    $category = Category::factory()->create(['name' => 'Motorcycle']);
    $unit = Unit::factory()->create([
        'category_id' => $category->id,
        'name' => 'Yamaha R1',
        'price_php' => 1150000,
        'status' => Unit::STATUS_AVAILABLE,
        'show_price' => true,
    ]);
    UnitImage::factory()->create([
        'unit_id' => $unit->id,
        'url' => 'units/'.$unit->id.'/yamaha-r1.jpg',
        'sort_order' => 0,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Yamaha R1')
        ->assertSee('Available')
        ->assertDontSee('Admin Panel')
        ->assertDontSee('Log in');

    $this->get(route('units.show', $unit))
        ->assertOk()
        ->assertSee('Yamaha R1')
        ->assertSee('Category: Motorcycle');
});

it('admin routes require authentication', function (): void {
    $unit = Unit::factory()->create();

    $this->get('/admin')->assertRedirect(route('login'));
    $this->get('/admin/units')->assertRedirect(route('login'));
    $this->get('/admin/units/create')->assertRedirect(route('login'));
    $this->get(route('admin.units.edit', $unit))->assertRedirect(route('login'));
    $this->get('/admin/categories')->assertRedirect(route('login'));
    $this->get('/admin/employees')->assertRedirect(route('login'));
    $this->get('/admin/logs')->assertRedirect(route('login'));
    $this->get($unit->signedQrUrl())->assertRedirect(route('login'));
    $this->post(route('admin.units.set-sold', $unit))->assertRedirect(route('login'));
    $this->post(route('admin.units.set-available', $unit))->assertRedirect(route('login'));
});

it('non-admin authenticated users cannot access admin routes', function (): void {
    $unit = Unit::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get('/admin')->assertForbidden();
    $this->get('/admin/units')->assertForbidden();
    $this->get('/admin/units/create')->assertForbidden();
    $this->get(route('admin.units.edit', $unit))->assertForbidden();
    $this->get('/admin/categories')->assertForbidden();
    $this->get('/admin/employees')->assertForbidden();
    $this->get('/admin/logs')->assertForbidden();
    $this->get($unit->signedQrUrl())->assertForbidden();
    $this->post(route('admin.units.set-sold', $unit))->assertForbidden();
    $this->post(route('admin.units.set-available', $unit))->assertForbidden();
});

it('admin users can create categories and units', function (): void {
    Storage::fake(config('filesystems.default'));

    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    Livewire::test(AdminCategories::class)
        ->set('name', 'SUV')
        ->call('create');

    $category = Category::query()->where('name', 'SUV')->first();

    expect($category)->not()->toBeNull();

    $upload = UploadedFile::fake()->image('new-unit.jpg');

    Livewire::test(AdminUnitForm::class)
        ->set('category_id', $category->id)
        ->set('name', 'Civic Type R')
        ->set('price_php', 2500000)
        ->set('description', 'Performance hatchback')
        ->set('show_price', true)
        ->set('newImages', [$upload])
        ->set('newImageSortOrders', [0])
        ->call('save')
        ->assertRedirect(route('admin.units.index'));

    $unit = Unit::query()->where('name', 'Civic Type R')->first();

    expect($unit)->not()->toBeNull();

    $image = UnitImage::query()->where('unit_id', $unit->id)->first();

    expect($image)->not()->toBeNull();
    expect($image->url)->toStartWith('units/'.$unit->id.'/');
    expect($image->url)->not()->toStartWith('http');
    expect(Storage::disk(config('filesystems.default'))->exists($image->url))->toBeTrue();
    expect($unit->status)->toBe(Unit::STATUS_AVAILABLE);
});

it('guests cannot change status', function (): void {
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $this->post(route('admin.units.set-sold', $unit))
        ->assertRedirect(route('login'));

    expect($unit->fresh()->status)->toBe(Unit::STATUS_AVAILABLE);
    expect(UnitStatusLog::query()->count())->toBe(0);
});

it('authenticated users can set sold and set available', function (): void {
    $user = User::factory()->admin()->create();
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $this->actingAs($user);

    $this->from($unit->signedQrUrl())
        ->post(route('admin.units.set-sold', $unit))
        ->assertRedirect($unit->signedQrUrl());

    expect($unit->fresh()->status)->toBe(Unit::STATUS_SOLD);

    $this->from($unit->signedQrUrl())
        ->post(route('admin.units.set-available', $unit))
        ->assertRedirect($unit->signedQrUrl());

    expect($unit->fresh()->status)->toBe(Unit::STATUS_AVAILABLE);

    expect(UnitStatusLog::query()->count())->toBe(2);
    expect(UnitStatusLog::query()->latest('id')->first()->action)->toBe(UnitStatusLog::ACTION_SET_AVAILABLE);
});

it('employee staff can set available from sold through qr workflow', function (): void {
    $employee = User::factory()->employee()->create();
    $unit = Unit::factory()->sold()->create();

    $qrUrl = $unit->signedQrUrl();

    $this->actingAs($employee)
        ->get($qrUrl)
        ->assertOk();

    $this->actingAs($employee)
        ->from($qrUrl)
        ->post(route('admin.units.set-available', $unit), [
            'request_id' => (string) Str::uuid(),
            'reason' => 'Correction after inventory check',
        ])
        ->assertRedirect($qrUrl);

    expect($unit->fresh()->status)->toBe(Unit::STATUS_AVAILABLE);

    $log = UnitStatusLog::query()->latest('id')->first();
    expect($log)->not()->toBeNull();
    expect($log?->action)->toBe(UnitStatusLog::ACTION_SET_AVAILABLE);
    expect($log?->user_id)->toBe($employee->id);
});

it('creates a log only when state changes', function (): void {
    $user = User::factory()->admin()->create();
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $this->actingAs($user)
        ->post(route('admin.units.set-sold', $unit));

    expect(UnitStatusLog::query()->count())->toBe(1);

    $log = UnitStatusLog::query()->first();
    expect($log->action)->toBe(UnitStatusLog::ACTION_SET_SOLD);
    expect($log->from_status)->toBe(Unit::STATUS_AVAILABLE);
    expect($log->to_status)->toBe(Unit::STATUS_SOLD);
});

it('is idempotent and does not create duplicate logs for the same state', function (): void {
    $user = User::factory()->admin()->create();
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $this->actingAs($user)
        ->post(route('admin.units.set-sold', $unit));

    $this->actingAs($user)
        ->post(route('admin.units.set-sold', $unit));

    expect($unit->fresh()->status)->toBe(Unit::STATUS_SOLD);
    expect(UnitStatusLog::query()->count())->toBe(1);
});

it('uses concurrency-safe set-state logic for stale reads', function (): void {
    $user = User::factory()->create();
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $service = app(UnitStatusService::class);

    $staleReadA = Unit::query()->findOrFail($unit->id);
    $staleReadB = Unit::query()->findOrFail($unit->id);

    $first = $service->setSold(
        unit: $staleReadA,
        userId: $user->id,
        requestId: (string) Str::uuid(),
        reason: null,
        ipAddress: '127.0.0.1',
        userAgent: 'test-agent-a',
    );
    $second = $service->setSold(
        unit: $staleReadB,
        userId: $user->id,
        requestId: (string) Str::uuid(),
        reason: null,
        ipAddress: '127.0.0.1',
        userAgent: 'test-agent-b',
    );

    expect($first['changed'])->toBeTrue();
    expect($second['changed'])->toBeFalse();
    expect(UnitStatusLog::query()->count())->toBe(1);
    expect($unit->fresh()->status)->toBe(Unit::STATUS_SOLD);
});

it('keeps status and audit log consistent with request context', function (): void {
    $admin = User::factory()->admin()->create();
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);
    $requestId = (string) Str::uuid();

    $this->actingAs($admin)
        ->from($unit->signedQrUrl())
        ->post(route('admin.units.set-sold', $unit), [
            'request_id' => $requestId,
            'reason' => 'Validated at pickup gate',
        ])
        ->assertRedirect($unit->signedQrUrl());

    $log = UnitStatusLog::query()->latest('id')->first();

    expect($unit->fresh()->status)->toBe(Unit::STATUS_SOLD);
    expect($log->action)->toBe(UnitStatusLog::ACTION_SET_SOLD);
    expect($log->from_status)->toBe(Unit::STATUS_AVAILABLE);
    expect($log->to_status)->toBe(Unit::STATUS_SOLD);
    expect($log->request_id)->toBe($requestId);
    expect($log->reason)->toBe('Validated at pickup gate');
});

it('rolls back status change if audit log write fails', function (): void {
    $unit = Unit::factory()->create([
        'status' => Unit::STATUS_AVAILABLE,
    ]);

    $service = app(UnitStatusService::class);

    expect(fn () => $service->setSold(
        unit: $unit,
        userId: 999999, // Invalid FK forces log insert failure.
        requestId: (string) Str::uuid(),
        reason: 'Rollback test',
        ipAddress: '127.0.0.1',
        userAgent: 'rollback-test',
    ))->toThrow(QueryException::class);

    expect($unit->fresh()->status)->toBe(Unit::STATUS_AVAILABLE);
    expect(UnitStatusLog::query()->count())->toBe(0);
});

it('does not allow mass-assigning admin privileges', function (): void {
    $user = User::factory()->create([
        'is_admin' => false,
    ]);

    $user->fill(['is_admin' => true]);
    $user->save();

    expect($user->fresh()->is_admin)->toBeFalse();
});

it('admin user seeder syncs only configured emails', function (): void {
    config()->set('showroom.admin_seed_emails', [
        'admin@example.com',
        'missing@example.com',
    ]);
    config()->set('showroom.admin_password', 'admin123');

    $target = User::factory()->create([
        'email' => 'admin@example.com',
        'is_admin' => false,
    ]);

    $untouched = User::factory()->create([
        'email' => 'staff@example.com',
        'is_admin' => false,
    ]);

    app(AdminUserSeeder::class)->run();

    expect($target->fresh()->is_admin)->toBeTrue();
    expect($untouched->fresh()->is_admin)->toBeFalse();
    expect(Hash::check('admin123', $target->fresh()->password))->toBeTrue();

    $created = User::query()->where('email', 'missing@example.com')->first();
    expect($created)->not()->toBeNull();
    expect($created?->is_admin)->toBeTrue();
    expect(Hash::check('admin123', (string) $created?->password))->toBeTrue();
});

it('admin can create employee account with default profile settings', function (): void {
    config()->set('showroom.employee_defaults', [
        'job_title' => 'Inventory Staff',
        'preferred_locale' => 'en_PH',
        'preferred_timezone' => 'Asia/Manila',
    ]);

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test(AdminEmployees::class)
        ->set('name', 'Employee One')
        ->set('email', 'employee.one@example.com')
        ->set('password', 'employee123')
        ->set('password_confirmation', 'employee123')
        ->set('phone', '+639171234567')
        ->call('create');

    $employee = User::query()->where('email', 'employee.one@example.com')->first();

    expect($employee)->not()->toBeNull();
    expect($employee?->is_employee)->toBeTrue();
    expect($employee?->is_admin)->toBeFalse();
    expect($employee?->job_title)->toBe('Inventory Staff');
    expect($employee?->preferred_locale)->toBe('en_PH');
    expect($employee?->preferred_timezone)->toBe('Asia/Manila');
    expect(Hash::check('employee123', (string) $employee?->password))->toBeTrue();
    expect($employee?->email_verified_at)->not()->toBeNull();
});
