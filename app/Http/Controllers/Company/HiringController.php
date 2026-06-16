<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Hiring;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HiringController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role !== 'company') abort(403, 'Chỉ tài khoản chủ Doanh nghiệp mới có quyền quản lý Nhân sự.');
        
        $company = auth()->user()->active_company;
        $hirings = Hiring::where('company_id', $company->id)->with('user')->latest()->paginate(10);
        return view('company.hirings.index', compact('hirings'));
    }

    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->role !== 'company') abort(403, 'Chỉ tài khoản chủ Doanh nghiệp mới có quyền quản lý Nhân sự.');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'position' => 'required|string|max:255',
        ]);

        $company = auth()->user()->active_company;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'hiring',
            'is_active' => true,
        ]);

        Hiring::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'position' => $request->position,
        ]);

        return redirect()->back()->with('success', 'Đã thêm tài khoản nhân sự thành công.');
    }

    public function destroy(Hiring $hiring)
    {
        if (auth()->check() && auth()->user()->role !== 'company') abort(403, 'Chỉ tài khoản chủ Doanh nghiệp mới có quyền quản lý Nhân sự.');
        
        $company = auth()->user()->active_company;
        if ($hiring->company_id !== $company->id) abort(403);

        $hiring->user->delete(); // Soft delete user
        $hiring->delete(); // Soft delete or hard delete depending on setup, but user is enough

        return redirect()->back()->with('success', 'Đã xóa tài khoản nhân sự.');
    }
}