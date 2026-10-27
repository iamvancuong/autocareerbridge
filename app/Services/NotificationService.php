<?php
namespace App\Services;

use App\Models\Notification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Gửi email kèm theo thông báo trong hệ thống.
     *
     * Máy chủ thư có thể tạm ngưng hoặc cấu hình sai, nhưng điều đó không được
     * phép làm hỏng nghiệp vụ đang chạy (duyệt hồ sơ, phê duyệt tài khoản...).
     * Vì vậy mọi lỗi gửi thư chỉ được ghi log, thông báo trong hệ thống vẫn còn.
     */
    public function mail(?string $email, Mailable $mailable): void
    {
        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::warning('Không gửi được email thông báo.', [
                'to'       => $email,
                'mailable' => $mailable::class,
                'error'    => $e->getMessage(),
            ]);
        }
    }

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
