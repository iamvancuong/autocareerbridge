<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentResumeController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student) return redirect()->back()->with('error', 'Chỉ sinh viên mới có quyền truy cập.');
        
        $resumes = Resume::where('student_id', $student->id)->orderByDesc('is_default')->latest()->get();
        return view('student.resumes.index', compact('resumes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $student = auth()->user()->student;
        if (!$student) return redirect()->back()->with('error', 'Chỉ sinh viên mới có quyền tải lên CV.');

        $file = $request->file('cv_file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('resumes', 'public');
        $isDefault = $request->has('is_default');

        if ($isDefault) {
            // Remove previous default
            Resume::where('student_id', $student->id)->update(['is_default' => false]);
        }

        Resume::create([
            'student_id' => $student->id,
            'file_path' => $path,
            'original_name' => $originalName,
            'is_default' => $isDefault,
            'content' => 'Nội dung trích xuất từ PDF (Mock content cho AI)' // In real app, parse PDF text here
        ]);

        return redirect()->back()->with('success', 'Tải CV lên thành công!');
    }

    public function setDefault(Resume $resume)
    {
        if ($resume->student_id !== auth()->user()->student->id) abort(403);
        
        Resume::where('student_id', $resume->student_id)->update(['is_default' => false]);
        $resume->update(['is_default' => true]);
        
        return redirect()->back()->with('success', 'Đã đặt làm CV mặc định!');
    }

    public function destroy(Resume $resume)
    {
        if ($resume->student_id !== auth()->user()->student->id) abort(403);
        
        Storage::disk('public')->delete($resume->file_path);
        $resume->delete();
        
        return redirect()->back()->with('success', 'Đã xóa CV.');
    }
}