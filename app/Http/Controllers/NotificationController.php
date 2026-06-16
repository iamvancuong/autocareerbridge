<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->paginate(15);
        // Mark all as read when visiting the page
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
        }
        return redirect($notification->url ?? route('notifications.index'));
    }
}