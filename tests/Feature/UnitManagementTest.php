<?php

namespace Tests\Feature;

use App\Livewire\AdminUnitForm;
use App\Livewire\AdminUnitsIndex;
use App\Models\Category;
use App\Models\Unit;
use App\Models\UnitStatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UnitManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_access_is_blocked(): void
    {
        $unit = Unit::factory()->create();

        $this->get(route('admin.units.index'))->assertRedirect(route('login'));
        $this->get(route('admin.units.create'))->assertRedirect(route('login'));
        $this->get(route('admin.units.edit', $unit))->assertRedirect(route('login'));
    }

    public function test_can_create_unit(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(AdminUnitForm::class)
            ->set('category_id', $category->id)
            ->set('name', 'Test Unit')
            ->set('price_php', 1000000)
            ->set('description', 'Test Description')
            ->set('show_price', true)
            ->set('is_featured', false)
            ->call('save')
            ->assertRedirect(route('admin.units.index'));

        $this->assertDatabaseHas('units', [
            'name' => 'Test Unit',
            'price_php' => 1000000,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('unit_status_logs', [
            'action' => UnitStatusLog::ACTION_CREATE,
            'user_id' => $user->id,
        ]);
    }

    public function test_soft_deletes_unit(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['name' => 'Delete Me']);

        Livewire::actingAs($user)
            ->test(AdminUnitsIndex::class)
            // 1. Test confirmation state
            ->call('confirmDelete', $unit->id)
            ->assertSet('unitToDeleteId', $unit->id)
            ->assertSet('unitToDeleteName', 'Delete Me')
            // 2. Test execution
            ->call('executeDelete')
            ->assertSet('unitToDeleteId', null)
            ->assertSet('unitToDeleteName', null);

        $this->assertSoftDeleted('units', ['id' => $unit->id]);

        $this->assertDatabaseHas('unit_status_logs', [
            'action' => UnitStatusLog::ACTION_DELETE,
            'unit_id' => $unit->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_can_mark_unit_as_sold_via_livewire(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        Livewire::actingAs($user)
            ->test(AdminUnitForm::class, ['unit' => $unit])
            ->set('statusReason', 'Sold to buyer')
            ->call('markAsSold')
            ->assertHasNoErrors();

        $this->assertEquals(Unit::STATUS_SOLD, $unit->fresh()->status);
        $this->assertDatabaseHas('unit_status_logs', [
            'unit_id' => $unit->id,
            'action' => UnitStatusLog::ACTION_SET_SOLD,
            'reason' => 'Sold to buyer',
            'user_id' => $user->id,
        ]);
    }

    public function test_can_mark_unit_as_available_via_livewire(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->sold()->create();

        Livewire::actingAs($user)
            ->test(AdminUnitForm::class, ['unit' => $unit])
            ->set('statusReason', 'Back in stock')
            ->call('markAsAvailable')
            ->assertHasNoErrors();

        $this->assertEquals(Unit::STATUS_AVAILABLE, $unit->fresh()->status);
        $this->assertDatabaseHas('unit_status_logs', [
            'unit_id' => $unit->id,
            'action' => UnitStatusLog::ACTION_SET_AVAILABLE,
            'reason' => 'Back in stock',
        ]);
    }

    public function test_can_mark_unit_as_sold_to_registered_collector_via_qr_action(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create(['is_admin' => false, 'is_employee' => false]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminUnitQrAction::class, ['unit' => $unit])
            ->set('is_guest', false)
            ->set('reason', 'QR scan sale')
            ->set('buyer_id', $customer->id)
            ->call('markAsSold')
            ->assertHasNoErrors();

        $this->assertEquals(Unit::STATUS_SOLD, $unit->fresh()->status);
        $this->assertEquals($customer->id, $unit->fresh()->buyer_id);
    }

    public function test_admin_unit_qr_action_defaults_is_guest_to_true_and_does_not_load_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        $test = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminUnitQrAction::class, ['unit' => $unit])
            ->assertSet('is_guest', true);

        $this->assertEmpty($test->get('users'));
    }

    public function test_admin_unit_qr_action_collector_query_excludes_staff_and_admin_accounts(): void
    {
        $admin = User::factory()->create(['name' => 'Admin User', 'is_admin' => true, 'is_employee' => false]);
        $employee = User::factory()->create(['name' => 'Staff User', 'is_admin' => false, 'is_employee' => true]);
        $customer = User::factory()->create(['name' => 'Customer User', 'is_admin' => false, 'is_employee' => false]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        $test = Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminUnitQrAction::class, ['unit' => $unit])
            ->set('is_guest', false);

        $users = $test->get('users');

        $this->assertTrue($users->contains('id', $customer->id));
        $this->assertFalse($users->contains('id', $admin->id));
        $this->assertFalse($users->contains('id', $employee->id));
    }

    public function test_admin_unit_qr_action_guest_sale_works_end_to_end(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);
        $file = \Illuminate\Http\UploadedFile::fake()->image('handover.jpg');

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminUnitQrAction::class, ['unit' => $unit])
            ->assertSet('is_guest', true)
            ->set('guest_name', 'Jane Doe')
            ->set('guest_contact', '+123456789')
            ->set('handover_image', $file)
            ->call('markAsSold')
            ->assertHasNoErrors()
            ->assertSet('is_guest', true)
            ->assertSet('guest_name', null);

        $unit->refresh();
        $this->assertEquals(Unit::STATUS_SOLD, $unit->status);
        $this->assertEquals('Jane Doe', $unit->guest_name);
        $this->assertEquals('+123456789', $unit->guest_contact);
        $this->assertNotNull($unit->handover_image_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($unit->handover_image_path);
    }

    public function test_default_inventory_index_lists_active_units_and_excludes_sold_units(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $activeUnit = Unit::factory()->create(['name' => 'Active Supercar', 'status' => Unit::STATUS_AVAILABLE]);
        $soldUnit = Unit::factory()->sold()->create(['name' => 'Sold Hypercar']);

        Livewire::actingAs($user)
            ->test(AdminUnitsIndex::class)
            ->assertSee('Active Supercar')
            ->assertDontSee('Sold Hypercar');
    }

    public function test_sold_archive_view_lists_sold_units_and_allows_relisting(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $activeUnit = Unit::factory()->create(['name' => 'Active Supercar', 'status' => Unit::STATUS_AVAILABLE]);
        $soldUnit = Unit::factory()->sold()->create(['name' => 'Sold Hypercar', 'guest_name' => 'John Doe']);

        Livewire::actingAs($user)
            ->test(AdminUnitsIndex::class)
            ->set('viewMode', 'sold')
            ->assertSee('Sold Hypercar')
            ->assertSee('John Doe')
            ->assertDontSee('Active Supercar')
            ->call('relistAsAvailable', $soldUnit->id)
            ->assertHasNoErrors();

        $this->assertEquals(Unit::STATUS_AVAILABLE, $soldUnit->fresh()->status);
    }
}
