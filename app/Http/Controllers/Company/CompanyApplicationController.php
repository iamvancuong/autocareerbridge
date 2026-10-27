<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class CompanyApplicationController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->active_company;
        if (!$company) abort(403);

        // Mặc định xếp theo điểm AI giảm dần để nhà tuyển dụng gặp ứng viên
        // tiềm năng nhất trước; 'newest' để quay về thứ tự nộp gần nhất.
        $sort = $request->input('sort') === 'newest' ? 'newest' : 'ai_score';

        // Mọi đơn ứng tuyển vào các tin tuyển dụng của doanh nghiệp này
        $baseQuery = fn() => Application::whereHas('job', function($q) use ($company) {
            $q->where('company_id', $company->id);
        });

        $applications = $baseQuery()
            ->with('student.user', 'student.university', 'job')
            // Hồ sơ chưa chấm được (ai_score = null) dồn xuống cuối, tránh bị
            // hiểu nhầm là ứng viên điểm thấp.
            ->when($sort === 'ai_score', fn($q) => $q->orderByRaw('ai_score IS NULL, ai_score DESC'))
            ->latest()
            // Hai hồ sơ nộp trong cùng một giây sẽ có created_at bằng nhau; thiếu
            // tiêu chí phụ thì thứ tự giữa chúng không xác định và có thể đổi
            // giữa các trang, làm hỏng phân trang.
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Thống kê phải đếm trên TOÀN BỘ dữ liệu, không phải trên trang hiện tại:
        // gọi count() trực tiếp trên paginator chỉ trả về số bản ghi của 1 trang.
        $countsByStatus = $baseQuery()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total'    => (int) $countsByStatus->sum(),
            'pending'  => (int) $countsByStatus->get('pending', 0),
            'accepted' => (int) $countsByStatus->get('accepted', 0),
            'rejected' => (int) $countsByStatus->get('rejected', 0),
        ];

        return view('company.applications.index', compact('applications', 'stats', 'sort'));
    }

    public function show(Application $application)
    {
        $company = auth()->user()->active_company;
        if ($application->job->company_id !== $company->id) abort(403);

        $application->load('student.user', 'student.university', 'student.major', 'job', 'decisions.user');
        
        // Find the resume the student used to apply. For now, assume default resume.
        $resume = \App\Models\Resume::where('student_id', $application->student_id)
            ->where('is_default', true)->first() 
            ?? \App\Models\Resume::where('student_id', $application->student_id)->latest()->first();

        return view('company.applications.show', compact('application', 'resume'));
    }

    public function update(Request $request, Application $application, \App\Services\NotificationService $notifService)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'hr_feedback' => 'nullable|string'
        ]);
        
        if ($application->job->company_id !== auth()->user()->active_company->id) abort(403);

        $actor = auth()->user();
        $previousStatus = $application->status;

        // Cập nhật trạng thái và ghi nhật ký phải đi liền nhau: nếu chỉ một trong
        // hai thành công thì hồ sơ sẽ mang trạng thái không giải thích được.
        \Illuminate\Support\Facades\DB::transaction(function () use ($application, $request, $actor, $previousStatus) {
            $application->update([
                'status' => $request->status,
                'hr_feedback' => $request->hr_feedback
            ]);

            $application->decisions()->create([
                'user_id'         => $actor->id,
                'decided_by_role' => $actor->role,
                'decided_by_name' => $actor->name,
                'previous_status' => $previousStatus,
                'status'          => $request->status,
                'hr_feedback'     => $request->hr_feedback,
            ]);
        });

        $jobTitle = $application->job->title;
        $companyName = auth()->user()->active_company->company_name;
        
        if ($request->status == 'accepted') {
             $notifService->send($application->student->user_id, 'Hồ sơ đã được chấp nhận', "Hồ sơ ứng tuyển của bạn cho vị trí {$jobTitle} tại {$companyName} đã được chấp nhận!", 'success');
        } else {
             $notifService->send($application->student->user_id, 'Hồ sơ bị từ chối', "Rất tiếc, hồ sơ ứng tuyển của bạn cho vị trí {$jobTitle} tại {$companyName} đã bị từ chối.", 'danger');
        }

        // Ngoài thông báo trong hệ thống, gửi thêm email để sinh viên biết kết quả
        // mà không cần chủ động đăng nhập vào xem.
        $application->load('student.user', 'job');
        $notifService->mail(
            $application->student->user->email ?? null,
            new \App\Mail\ApplicationStatusMail($application, $companyName)
        );

        return redirect()->back()->with('success', 'Đã ' . ($request->status == 'accepted' ? 'chấp nhận' : 'từ chối') . ' ứng viên!');
    }
}