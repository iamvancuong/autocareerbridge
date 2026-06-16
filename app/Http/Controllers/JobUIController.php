<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Student;
use App\Models\Resume;
use App\Models\Application;
use App\Models\Major;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class JobUIController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $majorId = $request->input('major_id');

        $query = Job::with('company', 'major')->where('is_approved', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($majorId) {
            $query->where('major_id', $majorId);
        }

        $allJobs = $query->get();
        $majors = Major::all();

        // Job Matching logic for Student
        $matchedJobs = collect();
        $otherJobs = collect();
        $partnerCompanyIds = [];

        if (auth()->check() && auth()->user()->role == 'student' && auth()->user()->student) {
            $student = auth()->user()->student;
            $studentMajorId = $student->major_id;
            
            // Get partner companies
            $partnerCompanyIds = \App\Models\Collaboration::where('university_id', $student->university_id)
                ->where('status', 'approved')
                ->pluck('company_id')->toArray();

            foreach ($allJobs as $job) {
                if ($job->major_id == $studentMajorId) {
                    $matchedJobs->push($job);
                } else {
                    $otherJobs->push($job);
                }
            }
            // Sort to show matched first
            $jobs = $matchedJobs->merge($otherJobs);
            $studentMajorIdForView = $studentMajorId;
        } else {
            $jobs = $allJobs;
            $studentMajorIdForView = null;
        }

        return view('jobs.index', compact('jobs', 'majors', 'search', 'majorId', 'studentMajorIdForView', 'partnerCompanyIds'));
    }

    public function show($id)
    {
        $job = Job::with('company')->findOrFail($id);
        $application = null;
        $isPartner = false;
        
        if (auth()->check() && auth()->user()->role == 'student' && auth()->user()->student) {
            $student = auth()->user()->student;
            
            $application = Application::where('job_id', $id)
                ->where('student_id', $student->id)
                ->first();
                
            $isPartner = \App\Models\Collaboration::where('university_id', $student->university_id)
                ->where('company_id', $job->company_id)
                ->where('status', 'approved')
                ->exists();
        }

        return view('jobs.show', compact('job', 'application', 'isPartner'));
    }

    public function apply(Request $request, $id, ApplicationService $applicationService, \App\Services\NotificationService $notifService)
    {
        if (!auth()->check() || auth()->user()->role != 'student') {
            return redirect()->back()->with('error', 'Chỉ sinh viên mới có quyền ứng tuyển.');
        }

        $job = Job::findOrFail($id);
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin sinh viên.');
        }

        // Check if already applied
        $existing = Application::where('job_id', $id)->where('student_id', $student->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Bạn đã nộp đơn vào vị trí này rồi!');
        }

        // Use student's default CV
        $resume = Resume::where('student_id', $student->id)->where('is_default', true)->first()
            ?? Resume::where('student_id', $student->id)->latest()->first();

        if (!$resume) {
            return redirect()->back()->with('error', 'Vui lòng tải lên ít nhất một CV trước khi ứng tuyển.');
        }

        try {
            $applicationService->apply($student, $job, $resume);
            
            // Notify the company
            $notifService->send(
                $job->company->user_id,
                'Ứng viên mới',
                "{$student->user->name} vừa ứng tuyển vào vị trí {$job->title}.",
                'info',
                route('company.applications.index')
            );
            
            return redirect()->back()->with('success', 'Đã nộp CV thành công! AI đang phân tích hồ sơ của bạn...');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}