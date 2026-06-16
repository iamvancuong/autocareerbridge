<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\Collaboration;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'companies' => User::where('role', 'company')->count(),
            'universities' => User::where('role', 'university')->count(),
            'students' => User::where('role', 'student')->count(),
            'jobs_pending' => Job::where('is_approved', false)->count(),
            'jobs_approved' => Job::where('is_approved', true)->count(),
            'applications' => Application::count(),
            'collaborations' => Collaboration::where('status', 'approved')->count(),
            'pending_accounts' => User::where('is_active', false)->count(),
        ];
        $recentJobs = Job::with('company')->latest()->take(5)->get();
        $recentUsers = User::whereNot('role', 'admin')->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentUsers'));
    }
}