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
            'content' => $this->extractText($file),
        ]);

        return redirect()->back()->with('success', 'Tải CV lên thành công!');
    }

    /**
     * Trích xuất văn bản trong CV để đưa sang AI chấm điểm.
     *
     * Chỉ đọc được .pdf. Với .doc/.docx (hoặc PDF scan ảnh, PDF đặt mật khẩu)
     * ta trả về một ghi chú nêu rõ lý do thay vì bịa nội dung — nếu đưa dữ liệu
     * giả cho AI thì điểm số chấm ra sẽ vô nghĩa mà HR không hề hay biết.
     */
    private function extractText(\Illuminate\Http\UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension !== 'pdf') {
            return "[Chưa trích xuất được nội dung] Hệ thống hiện chỉ đọc được tệp PDF, "
                 . "tệp tải lên có định dạng .{$extension}. Vui lòng xem trực tiếp tệp CV đính kèm.";
        }

        try {
            $text = (new \Smalot\PdfParser\Parser())->parseFile($file->getRealPath())->getText();
            $text = trim(preg_replace('/\s+/u', ' ', $text));

            if ($text === '') {
                return '[Chưa trích xuất được nội dung] Tệp PDF không chứa lớp văn bản '
                     . '(có thể là bản scan thành ảnh). Vui lòng xem trực tiếp tệp CV đính kèm.';
            }

            // Giới hạn độ dài để không thổi phồng prompt gửi sang dịch vụ AI.
            return \Illuminate\Support\Str::limit($text, 15000, '… [nội dung đã được cắt bớt]');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Không đọc được nội dung CV.', [
                'file'  => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
            ]);

            return '[Chưa trích xuất được nội dung] Không đọc được tệp PDF này. '
                 . 'Vui lòng xem trực tiếp tệp CV đính kèm.';
        }
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