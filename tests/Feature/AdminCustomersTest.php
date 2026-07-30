<?php

use App\Livewire\AdminCustomersIndex;
use App\Models\Bid;
use App\Models\BidDeposit;
use App\Models\ChatMessage;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin and owner can view customers management page', function () {
    $admin = User::factory()->create(['is_admin' => true, 'is_employee' => true, 'job_title' => 'Admin']);
    $owner = User::factory()->create(['is_admin' => true, 'is_employee' => true, 'job_title' => 'Owner']);

    $this->actingAs($admin)
        ->get('/admin/customers')
        ->assertOk();

    $this->actingAs($owner)
        ->get('/admin/customers')
        ->assertOk();
});

test('staff and guests cannot access customers management page', function () {
    $staff = User::factory()->create(['is_admin' => false, 'is_employee' => true, 'job_title' => 'Staff']);
    $customer = User::factory()->create(['is_admin' => false, 'is_employee' => false]);

    $this->get('/admin/customers')
        ->assertRedirect(route('login'));

    $this->actingAs($staff)
        ->get('/admin/customers')
        ->assertForbidden();

    $this->actingAs($customer)
        ->get('/admin/customers')
        ->assertForbidden();
});

test('customers index displays activity counts and details', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false, 'is_employee' => false]);
    $unit = Unit::factory()->create();

    $auction = \App\Models\Auction::query()->create([
        'unit_id' => $unit->id,
        'lot_number' => 'LOT-0001',
        'start_at' => now()->subHour(),
        'end_at' => now()->addHour(),
        'reserve_price_php' => 1000000,
        'starting_bid_php' => 500000,
        'current_bid_php' => 500000,
        'status' => 'live',
    ]);
    $customer->savedUnits()->attach($unit->id);
    Bid::create(['user_id' => $customer->id, 'auction_id' => $auction->id, 'amount_php' => 1000000]);
    BidDeposit::create([
        'user_id' => $customer->id,
        'auction_id' => $auction->id,
        'amount' => 100000,
        'proof_image' => 'deposits/proof.jpg',
        'status' => 'pending',
    ]);
    ChatMessage::create(['user_id' => $customer->id, 'unit_id' => $unit->id, 'body' => 'Inquiry message', 'is_from_admin' => false]);

    $this->actingAs($admin);

    Livewire::test(AdminCustomersIndex::class)
        ->assertSee($customer->name)
        ->assertSee($customer->email)
        ->assertSee('1 Saved')
        ->assertSee('1 Bids')
        ->assertSee('1 Deposits')
        ->assertSee('1 Chats');
});

test('admin can block and unblock a customer account', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false, 'is_employee' => false, 'is_blocked' => false]);

    $this->actingAs($admin);

    Livewire::test(AdminCustomersIndex::class)
        ->call('toggleBlock', $customer->id);

    expect($customer->fresh()->is_blocked)->toBeTrue();
    expect($customer->fresh()->isBlocked())->toBeTrue();

    Livewire::test(AdminCustomersIndex::class)
        ->call('toggleBlock', $customer->id);

    expect($customer->fresh()->is_blocked)->toBeFalse();
});

test('blocked customer is logged out and cannot participate in auctions', function () {
    $customer = User::factory()->create(['is_admin' => false, 'is_employee' => false, 'is_blocked' => true]);

    expect($customer->canParticipateInAuctions())->toBeFalse();

    $this->actingAs($customer)
        ->get(route('garage'))
        ->assertRedirect(route('home'))
        ->assertSessionHas('error');

    $this->assertGuest();
});
