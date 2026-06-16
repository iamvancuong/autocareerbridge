<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Major;
use Illuminate\Http\Request;

class CompanyJobController extends Controller
{
    public function index()
    {
        $company = auth()->user()->active_company;
        if (!$company) return redirect()->back()->with('error', 'Không tìm thấy thông tin doanh nghiệp.');
        
        $jobs = Job::where('company_id', $company->id)->with('major')->latest()->paginate(10);
        return view('company.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $majors = Major::all();
        return view('company.jobs.create', compact('majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
        ]);

        $company = auth()->user()->active_company;
        
        Job::create([
            'company_id' => $company->id,
            'major_id' => $request->major_id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'is_approved' => false, // Require admin approval
        ]);

        return redirect()->route('company.jobs.index')->with('success', 'Đăng tin thành công. Vui lòng chờ Admin duyệt!');
    }

    public function edit(Job $job)
    {
        if ($job->company_id !== auth()->user()->active_company->id) abort(403);
        $majors = Major::all();
        return view('company.jobs.edit', compact('job', 'majors'));
    }

    public function update(Request $request, Job $job)
    {
        if ($job->company_id !== auth()->user()->active_company->id) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
        ]);

        $job->update([
            'title' => $request->title,
            'major_id' => $request->major_id,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'is_approved' => false, // Reset approval on edit
        ]);

        return redirect()->route('company.jobs.index')->with('success', 'Cập nhật tin thành công. Đang chờ duyệt lại!');
    }

    public function destroy(Job $job)
    {
        if ($job->company_id !== auth()->user()->active_company->id) abort(403);
        $job->delete();
        return redirect()->route('company.jobs.index')->with('success', 'Đã xóa tin tuyển dụng.');
    }
}