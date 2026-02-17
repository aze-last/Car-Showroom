<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_unit(): void
    {
        $category = Category::factory()->create();

        $unit = Unit::factory()->create([
            'category_id' => $category->id,
            'name' => 'Toyota Vios',
            'price_php' => 750000,
        ]);

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'name' => 'Toyota Vios',
            'price_php' => 750000,
            'status' => Unit::STATUS_AVAILABLE,
        ]);

        $this->assertNotNull($unit->public_id);
    }

    public function test_soft_deletes_unit(): void
    {
        $unit = Unit::factory()->create();

        $unit->delete();

        $this->assertSoftDeleted($unit);
    }

    public function test_status_change_to_sold(): void
    {
        $unit = Unit::factory()->create(['status' => Unit::STATUS_AVAILABLE]);

        // Assuming we implement a method for this action to encapsulate logic
        $unit->markAsSold();

        $this->assertEquals(Unit::STATUS_SOLD, $unit->fresh()->status);
    }

    public function test_revert_to_available(): void
    {
        $unit = Unit::factory()->sold()->create();

        // Assuming we implement a method for this action
        $unit->markAsAvailable();

        $this->assertEquals(Unit::STATUS_AVAILABLE, $unit->fresh()->status);
    }

    public function test_prevents_double_sell(): void
    {
        $unit = Unit::factory()->sold()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unit is already sold.');

        $unit->markAsSold();
    }
}
