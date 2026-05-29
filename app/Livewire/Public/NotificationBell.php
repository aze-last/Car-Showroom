<?php

namespace App\Livewire\Public;

use Livewire\Component;

class NotificationBell extends Component
{
    public function markAsRead($notificationId)
    {
        if (auth()->check()) {
            auth()->user()->notifications()->findOrFail($notificationId)->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
    }

    public function render()
    {
        $notifications = auth()->check()
            ? auth()->user()->notifications()->latest()->take(10)->get()
            : collect();

        $unreadCount = auth()->check()
            ? auth()->user()->unreadNotifications->count()
            : 0;

        return view('livewire.public.notification-bell', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
