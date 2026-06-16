<?php
namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicAffair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AcademicAffairController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role !== 'university') abort(403, 'Chỉ tài khoản chủ Trường học mới có quyền quản lý Giáo vụ.');

        $university = auth()->user()->active_university;
        $staff = AcademicAffair::where('university_id', $university->id)->with('user')->latest()->paginate(10);
        return view('university.academic_affairs.index', compact('staff'));
    }

    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'university') abort(403, 'Chỉ tài khoản chủ Trường học mới có quyền quản lý Giáo vụ.');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'department' => 'required|string|max:255',
        ]);

        $university = auth()->user()->active_university;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'academic_affairs',
            'is_active' => true,
        ]);

        AcademicAffair::create([
            'user_id' => $user->id,
            'university_id' => $university->id,
            'department' => $request->department,
        ]);

        return redirect()->back()->with('success', 'Đã thêm tài khoản giáo vụ thành công.');
    }

    public function destroy(AcademicAffair $academicAffair)
    {
        if (auth()->check() && auth()->user()->role !== 'university') abort(403, 'Chỉ tài khoản chủ Trường học mới có quyền quản lý Giáo vụ.');

        $university = auth()->user()->active_university;
        if ($academicAffair->university_id !== $university->id) abort(403);

        $academicAffair->user->delete();
        $academicAffair->delete();

        return redirect()->back()->with('success', 'Đã xóa tài khoản giáo vụ.');
    }
}