<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $pendingUsers = User::where('is_active', false)->whereIn('role', ['company', 'university'])->with('company', 'university')->latest()->paginate(10);
        $allUsers = User::whereNot('role', 'admin')->with('company', 'university')->latest()->paginate(15);
        return view('admin.users.index', compact('pendingUsers', 'allUsers'));
    }

    public function approve(User $user)
    {
        $user->update(['is_active' => true]);
        return redirect()->back()->with('success', 'Đã phê duyệt tài khoản ' . $user->name);
    }

    public function reject(User $user)
    {
        $user->update(['is_active' => false]);
        return redirect()->back()->with('success', 'Đã từ chối tài khoản ' . $user->name);
    }
}