<?php
namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function send(int $userId, string $title, string $message, string $type = 'info', string $url = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'url'     => $url,
            'is_read' => false,
        ]);
    }

    public function markAllRead(int $userId): void
    {
        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);
    }

    public function unreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }
}
