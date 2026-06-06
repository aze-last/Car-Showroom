<?php

use App\Models\ChatMessage;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('authenticated users can send a message', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $this->actingAs($user);

    Livewire::test('public.chat-inquiry', ['unit' => $unit])
        ->set('body', 'Is this still available?')
        ->call('sendMessage')
        ->assertDispatched('message-sent');

    expect(ChatMessage::count())->toBe(1);
    expect(ChatMessage::first()->body)->toBe('Is this still available?');
});

test('auto-reply triggers after 30 seconds of inactivity', function () {
    $user = User::factory()->create();
    $unit = Unit::factory()->create();

    $this->actingAs($user);

    // Use DB to bypass Eloquent's timestamp management for setup
    DB::table('chat_messages')->insert([
        'user_id' => $user->id,
        'unit_id' => $unit->id,
        'body' => 'Test message',
        'is_from_admin' => false,
        'is_automated' => false,
        'created_at' => now()->subSeconds(35),
        'updated_at' => now()->subSeconds(35),
    ]);

    Livewire::test('public.chat-inquiry', ['unit' => $unit])
        ->call('checkAutoReply')
        ->assertDispatched('message-received');

    expect(ChatMessage::count())->toBe(2);
    expect(ChatMessage::latest()->first()->is_automated)->toBeTrue();
});
