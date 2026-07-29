<?php

namespace App\Livewire\Public;

use App\Concerns\EnforcesCollectorAuthentication;
use App\Concerns\HandlesLivewireErrors;
use App\Models\ChatMessage;
use App\Models\Setting;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatInquiry extends Component
{
    use EnforcesCollectorAuthentication;
    use HandlesLivewireErrors;

    #[Locked]
    public Unit $unit;

    public bool $isOpen = false;

    public bool $isMinimized = false;

    public string $body = '';

    public function mount(Unit $unit): void
    {
        $this->unit = $unit;

        // Auto-open if expandChat is present in URL (from notification)
        if (request()->query('expandChat')) {
            $this->isOpen = true;
        }
    }

    #[On('open-chat')]
    public function open(): void
    {
        $this->isOpen = true;
        $this->isMinimized = false;
        $this->dispatch('chat-opened');
    }

    public function minimize(): void
    {
        $this->isMinimized = true;
    }

    public function expand(): void
    {
        $this->isMinimized = false;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->isMinimized = false;
    }

    public function sendMessage(): void
    {
        if ($this->redirectIfGuest() || $this->redirectIfUnverified()) {
            return;
        }

        $this->validate(['body' => 'required|string|max:1000']);

        $created = $this->safely(fn () => ChatMessage::create([
            'user_id' => auth()->id(),
            'unit_id' => $this->unit->id,
            'body' => $this->body,
            'is_from_admin' => false,
        ]), 'Could not send your message. Please try again.', ['unit_id' => $this->unit->id]);

        if ($created === null) {
            return;
        }

        // Notify Admins — the message is already saved; notification failures
        // are logged without discarding it.
        $this->safely(function () {
            $admins = \App\Models\User::where('is_admin', true)->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\UserSentMessageNotification(auth()->user(), $this->unit, $this->body));
        }, 'Message sent, but the gallery could not be notified.', ['unit_id' => $this->unit->id]);

        $this->body = '';
        $this->dispatch('message-sent');
    }

    public function getMessagesProperty()
    {
        if (! auth()->check()) {
            return collect();
        }

        return ChatMessage::query()
            ->where('user_id', auth()->id())
            ->where('unit_id', $this->unit->id)
            ->oldest()
            ->get();
    }

    public function checkAutoReply(): void
    {
        if (! auth()->check()) {
            return;
        }

        $lastMessage = ChatMessage::query()
            ->where('user_id', auth()->id())
            ->where('unit_id', $this->unit->id)
            ->latest()
            ->first();

        if ($lastMessage && ! $lastMessage->is_from_admin && ! $lastMessage->is_automated) {
            $secondsSinceLastMessage = $lastMessage->created_at->diffInSeconds(now());

            if ($secondsSinceLastMessage >= 30) {
                // Poll-driven; log failures silently instead of toasting every poll.
                $this->safely(function () {
                    $shopName = Setting::get('shop_name', 'The Gallery');

                    ChatMessage::create([
                        'user_id' => auth()->id(),
                        'unit_id' => $this->unit->id,
                        'body' => "Greetings from {$shopName}. Our curators are currently assisting other collectors. Please leave your specific questions, and we will prioritize your inquiry shortly.",
                        'is_from_admin' => true,
                        'is_automated' => true,
                    ]);

                    $this->dispatch('message-received');
                }, null, ['unit_id' => $this->unit->id]);
            }
        }
    }

    public function render(): View
    {
        return view('livewire.public.chat-inquiry', [
            'messages' => $this->messages,
            'shopName' => Setting::get('shop_name', 'The Gallery'),
            'logo' => Setting::get('design_logo_path'),
        ]);
    }
}
