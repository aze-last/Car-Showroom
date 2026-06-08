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

    public function test_can_mark_unit_as_sold_via_qr_action_livewire(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\AdminUnitQrAction::class, ['unit' => $unit])
            ->set('reason', 'QR scan sale')
            ->set('buyer_id', $user->id)
            ->call('markAsSold')
            ->assertHasNoErrors();

        $this->assertEquals(Unit::STATUS_SOLD, $unit->fresh()->status);
    }
}
