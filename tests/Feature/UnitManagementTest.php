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
        $this->post(route('admin.units.set-sold', $unit))->assertRedirect(route('login'));
        $this->post(route('admin.units.set-available', $unit))->assertRedirect(route('login'));
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

    public function test_status_change_to_sold(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        $this->actingAs($user)
            ->post(route('admin.units.set-sold', $unit))
            ->assertRedirect();

        $this->assertEquals(Unit::STATUS_SOLD, $unit->fresh()->status);

        $this->assertDatabaseHas('unit_status_logs', [
            'action' => UnitStatusLog::ACTION_SET_SOLD,
            'unit_id' => $unit->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_revert_to_available(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->sold()->create();

        $this->actingAs($user)
            ->post(route('admin.units.set-available', $unit))
            ->assertRedirect();

        $this->assertEquals(Unit::STATUS_AVAILABLE, $unit->fresh()->status);

        $this->assertDatabaseHas('unit_status_logs', [
            'action' => UnitStatusLog::ACTION_SET_AVAILABLE,
            'unit_id' => $unit->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_logs_user_identity_on_status_change(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        $this->actingAs($user)
            ->post(route('admin.units.set-sold', $unit));

        $log = UnitStatusLog::where('unit_id', $unit->id)
            ->where('action', UnitStatusLog::ACTION_SET_SOLD)
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($user->id, $log->user_id);
    }
}
