<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function index()
    {
        // Show all pending jobs
        $jobs = Job::where('is_approved', false)->with('company', 'major')->latest()->paginate(10);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function approve(Job $job)
    {
        $job->update(['is_approved' => true]);
        return redirect()->route('admin.jobs.index')->with('success', 'Đã phê duyệt tin tuyển dụng!');
    }
}