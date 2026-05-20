<?php

namespace Tests\Feature;

use App\Livewire\AdminCategories;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModuleCompletenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_access_works(): void
    {
        $unit = Unit::factory()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($unit->name);

        $this->get(route('units.show', $unit))
            ->assertOk()
            ->assertSee($unit->name);
    }

    public function test_admin_can_manage_categories(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($user)
            ->test(AdminCategories::class)
            ->set('name', 'Motorcycles')
            ->call('create')
            ->assertSee('Category created.');

        $this->assertDatabaseHas('categories', ['name' => 'Motorcycles']);
    }

    public function test_qr_page_requires_auth_or_signature(): void
    {
        $unit = Unit::factory()->create();

        // Guest accessing directly
        $this->get(route('admin.units.qr', $unit))
            ->assertRedirect(route('login'));

        $this->get($unit->signedQrUrl())
            ->assertRedirect(route('login'));

        // Admin accessing signed URL
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user)
            ->get($unit->signedQrUrl())
            ->assertOk()
            ->assertSee($unit->name);
    }
}
