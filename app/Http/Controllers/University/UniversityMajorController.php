<?php
namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;

class UniversityMajorController extends Controller
{
    // Removed constructor middleware

    public function index()
    {
        if (!auth()->user()->hasUniversityRole()) abort(403, 'Chỉ tài khoản thuộc Trường học mới có quyền truy cập.');
        
        $university = auth()->user()->active_university;
        
        if (!$university) {
            return redirect()->route('profile.show')->with('error', 'Vui lòng cập nhật thông tin hồ sơ trường học trước khi quản lý ngành học.');
        }

        $allMajors = Major::all();
        $universityMajors = $university->majors->pluck('id')->toArray();
        
        return view('university.majors.index', compact('allMajors', 'universityMajors'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasUniversityRole()) abort(403, 'Chỉ tài khoản thuộc Trường học mới có quyền truy cập.');

        $university = auth()->user()->active_university;
        
        if (!$university) {
            return redirect()->route('profile.show')->with('error', 'Vui lòng cập nhật thông tin hồ sơ trường học trước khi quản lý ngành học.');
        }

        $majors = $request->input('majors', []);
        
        $university->majors()->sync($majors);
        
        return redirect()->back()->with('success', 'Đã cập nhật danh sách chuyên ngành đào tạo.');
    }
}