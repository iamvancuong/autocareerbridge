<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyReportController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasCompanyRole()) abort(403);
        $company = auth()->user()->active_company;
        
        // 1. Status Distribution
        $statusCounts = Application::whereHas('job', function($q) use ($company) {
            $q->where('company_id', $company->id);
        })
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

        $statusData = [
            $statusCounts['pending'] ?? 0,
            $statusCounts['reviewed'] ?? 0,
            $statusCounts['accepted'] ?? 0,
            $statusCounts['rejected'] ?? 0,
        ];

        // 2. Applications Over Last 30 Days
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        $dailyApps = Application::whereHas('job', function($q) use ($company) {
            $q->where('company_id', $company->id);
        })
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->keyBy('date');

        $labels = [];
        $trendData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            $trendData[] = isset($dailyApps[$date]) ? $dailyApps[$date]->total : 0;
        }

        // 3. Top Jobs by Applications
        $topJobs = Job::where('company_id', $company->id)
            ->withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get();

        $jobLabels = $topJobs->pluck('title')->toArray();
        $jobData = $topJobs->pluck('applications_count')->toArray();

        return view('company.reports.index', compact('statusData', 'labels', 'trendData', 'jobLabels', 'jobData'));
    }
}