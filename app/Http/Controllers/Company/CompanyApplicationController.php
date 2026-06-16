<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class CompanyApplicationController extends Controller
{
    public function index()
    {
        $company = auth()->user()->active_company;
        if (!$company) abort(403);

        // Get all applications for jobs belonging to this company
        $applications = Application::whereHas('job', function($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with('student.user', 'student.university', 'job')->latest()->paginate(10);

        // Stats
        $stats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        return view('company.applications.index', compact('applications', 'stats'));
    }

    public function show(Application $application)
    {
        $company = auth()->user()->active_company;
        if ($application->job->company_id !== $company->id) abort(403);

        $application->load('student.user', 'student.university', 'student.major', 'job');
        
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

        $application->update([
            'status' => $request->status,
            'hr_feedback' => $request->hr_feedback
        ]);
        
        $jobTitle = $application->job->title;
        $companyName = auth()->user()->active_company->company_name;
        
        if ($request->status == 'accepted') {
             $notifService->send($application->student->user_id, 'Hồ sơ đã được chấp nhận', "Hồ sơ ứng tuyển của bạn cho vị trí {$jobTitle} tại {$companyName} đã được chấp nhận!", 'success');
        } else {
             $notifService->send($application->student->user_id, 'Hồ sơ bị từ chối', "Rất tiếc, hồ sơ ứng tuyển của bạn cho vị trí {$jobTitle} tại {$companyName} đã bị từ chối.", 'danger');
        }

        return redirect()->back()->with('success', 'Đã ' . ($request->status == 'accepted' ? 'chấp nhận' : 'từ chối') . ' ứng viên!');
    }
}