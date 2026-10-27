<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        // Hai danh sách cùng nằm trên một trang nên phải tách tên tham số phân
        // trang; nếu cùng dùng `page` thì bấm sang trang 2 sẽ kéo theo cả danh
        // sách còn lại nhảy trang.
        // Seeder tạo hàng chục tài khoản trong cùng một giây nên latest() đơn lẻ
        // không đủ định thứ tự: MySQL trả về khác nhau giữa các lần truy vấn,
        // khiến cùng một tài khoản nhảy qua lại giữa các trang. orderByDesc('id')
        // là tiêu chí phụ chốt thứ tự lại.
        $pendingUsers = User::where('is_active', false)->whereNull('rejected_at')->whereIn('role', ['company', 'university'])->with('company', 'university')->latest()->orderByDesc('id')->paginate(10, ['*'], 'pending_page');
        $allUsers = User::whereNot('role', 'admin')->with('company', 'university')->latest()->orderByDesc('id')->paginate(15);
        return view('admin.users.index', compact('pendingUsers', 'allUsers'));
    }

    public function approve(User $user, \App\Services\NotificationService $notifService)
    {
        // Xóa dấu từ chối cũ để quản trị viên đảo lại quyết định được.
        $user->update(['is_active' => true, 'rejected_at' => null]);

        $notifService->send(
            $user->id,
            'Tài khoản đã được phê duyệt',
            'Quản trị viên đã phê duyệt tài khoản của bạn. Bạn có thể đăng nhập và sử dụng hệ thống.',
            'success'
        );
        $notifService->mail($user->email, new \App\Mail\AccountApprovedMail($user));

        return redirect()->back()->with('success', 'Đã phê duyệt tài khoản ' . $user->name);
    }

    public function reject(User $user)
    {
        // Đánh dấu mốc từ chối để tài khoản rời khỏi hàng chờ. Giữ nguyên bản
        // ghi thay vì xóa, để danh sách "Tất cả tài khoản" còn truy vết được.
        $user->update(['is_active' => false, 'rejected_at' => now()]);

        return redirect()->back()->with('success', 'Đã từ chối tài khoản ' . $user->name);
    }
}