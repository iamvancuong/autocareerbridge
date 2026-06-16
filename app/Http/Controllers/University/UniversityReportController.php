<?php
namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Application;
use Illuminate\Http\Request;

class UniversityReportController extends Controller
{
    public function index()
    {
        $university = auth()->user()->active_university;
        if (!$university) abort(403);

        $universityId = $university->id;

        // Stats
        $totalStudents = Student::where('university_id', $universityId)->count();
        
        $totalApplications = Application::whereHas('student', function($q) use ($universityId) {
            $q->where('university_id', $universityId);
        })->count();

        $totalAccepted = Application::whereHas('student', function($q) use ($universityId) {
            $q->where('university_id', $universityId);
        })->where('status', 'accepted')->count();

        // 1. Student distribution by Major
        $majorsData = Student::where('university_id', $universityId)
            ->whereNotNull('major_id')
            ->selectRaw('major_id, count(*) as total')
            ->with('major')
            ->groupBy('major_id')
            ->get();
            
        $majorLabels = $majorsData->map(fn($item) => $item->major->name ?? 'Unknown')->toArray();
        $majorCounts = $majorsData->map(fn($item) => $item->total)->toArray();

        // 2. Application Status Distribution
        $statusData = Application::whereHas('student', function($q) use ($universityId) {
            $q->where('university_id', $universityId);
        })
        ->selectRaw('status, count(*) as total')
        ->groupBy('status')
        ->get();

        $statusLabels = [];
        $statusCounts = [];
        $statusColors = [];

        foreach($statusData as $data) {
            if($data->status == 'pending') { $statusLabels[] = 'Chờ duyệt'; $statusColors[] = '#ffc107'; }
            elseif($data->status == 'reviewed') { $statusLabels[] = 'Đã xem'; $statusColors[] = '#17a2b8'; }
            elseif($data->status == 'accepted') { $statusLabels[] = 'Chấp nhận'; $statusColors[] = '#198754'; }
            elseif($data->status == 'rejected') { $statusLabels[] = 'Từ chối'; $statusColors[] = '#dc3545'; }
            $statusCounts[] = $data->total;
        }

        return view('university.reports.index', compact(
            'totalStudents', 'totalApplications', 'totalAccepted',
            'majorLabels', 'majorCounts',
            'statusLabels', 'statusCounts', 'statusColors'
        ));
    }
}
