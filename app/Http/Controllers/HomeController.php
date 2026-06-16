<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Workshop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = Job::with('company', 'major')
            ->where('is_approved', true)
            ->latest()
            ->take(3)
            ->get();

        $upcomingWorkshops = Workshop::with('university')
            ->whereNotNull('date')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->take(3)
            ->get();

        return view('home', compact('featuredJobs', 'upcomingWorkshops'));
    }

        public function dashboard()
    {
        $user = auth()->user();

        if ($user->hasCompanyRole() && $user->active_company) {
            $company = $user->active_company;
            $stats = [
                'total_jobs' => \App\Models\Job::where('company_id', $company->id)->count(),
                'pending_jobs' => \App\Models\Job::where('company_id', $company->id)->where('is_approved', false)->count(),
                'total_applications' => \App\Models\Application::whereHas('job', function($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->count(),
                'collaborations' => \App\Models\Collaboration::where('company_id', $company->id)->where('status', 'approved')->count(),
            ];
            $recentJobs = \App\Models\Job::where('company_id', $company->id)->latest()->take(5)->get();
            $recentApps = \App\Models\Application::whereHas('job', function($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->with('student.user', 'job')->latest()->take(5)->get();
            return view('dashboards.company', compact('stats', 'recentJobs', 'recentApps'));
        }

        if ($user->hasUniversityRole() && $user->active_university) {
            $university = $user->active_university;
            $stats = [
                'students' => \App\Models\Student::where('university_id', $university->id)->count(),
                'collaborations' => \App\Models\Collaboration::where('university_id', $university->id)->where('status', 'approved')->count(),
                'pending_collabs' => \App\Models\Collaboration::where('university_id', $university->id)->where('status', 'pending')->where('initiated_by', 'company')->count(),
                'workshops' => \App\Models\Workshop::where('university_id', $university->id)->count(),
            ];
            $pendingCollabs = \App\Models\Collaboration::where('university_id', $university->id)->where('status', 'pending')->where('initiated_by', 'company')->with('company')->latest()->take(5)->get();
            $upcomingWorkshops = \App\Models\Workshop::where('university_id', $university->id)->whereNotNull('date')->where('date', '>=', now()->toDateString())->orderBy('date')->take(5)->get();
            return view('dashboards.university', compact('stats', 'pendingCollabs', 'upcomingWorkshops'));
        }

        if ($user->role === 'student' && $user->student) {
            $student = $user->student;
            $applications = \App\Models\Application::where('student_id', $student->id)->with('job.company')->latest()->get();
            $stats = [
                'total_apps' => $applications->count(),
                'pending' => $applications->where('status', 'pending')->count(),
                'accepted' => $applications->where('status', 'accepted')->count(),
                'resumes' => \App\Models\Resume::where('student_id', $student->id)->count(),
            ];
            $suggestedJobs = \App\Models\Job::where('major_id', $student->major_id)->where('is_approved', true)->with('company')->latest()->take(5)->get();
            return view('dashboards.student', compact('stats', 'applications', 'suggestedJobs'));
        }
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('dashboard'); // fallback
    }
}