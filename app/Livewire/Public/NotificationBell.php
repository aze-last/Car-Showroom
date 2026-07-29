<?php

namespace App\Livewire\Public;

use App\Concerns\HandlesLivewireErrors;
use Livewire\Component;

class NotificationBell extends Component
{
    use HandlesLivewireErrors;

    public function markAsRead($notificationId)
    {
        if (auth()->check()) {
            $notification = auth()->user()->notifications()->findOrFail($notificationId);

            $this->safely(fn () => $notification->markAsRead(), 'Could not update the notification.', [
                'notification_id' => $notificationId,
            ]);
        }
    }

    public function markAllAsRead()
    {
        if (auth()->check()) {
            $this->safely(
                fn () => auth()->user()->unreadNotifications->markAsRead(),
                'Could not update your notifications.',
            );
        }
    }

    public function handleNotificationClick($notificationId)
    {
        if (! auth()->check()) {
            return;
        }

        $notification = auth()->user()->notifications()->findOrFail($notificationId);

        // Navigation matters more than read-marking; log failures silently.
        $this->safely(fn () => $notification->markAsRead(), null, [
            'notification_id' => $notificationId,
        ]);

        $actionUrl = $notification->data['action_url'] ?? null;

        if ($actionUrl) {
            return $this->redirect($actionUrl, navigate: true);
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
