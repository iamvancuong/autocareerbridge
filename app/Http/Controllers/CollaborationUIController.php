<?php
namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Services\CollaborationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollaborationUIController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->hasCompanyRole() && $user->active_company) {
            $collaborations = Collaboration::where('company_id', $user->active_company->id)->with('university')->latest()->paginate(10);
        } elseif ($user->hasUniversityRole() && $user->active_university) {
            $collaborations = Collaboration::where('university_id', $user->active_university->id)->with('company')->latest()->paginate(10);
        } else {
            $collaborations = Collaboration::where('id', 0)->paginate(10);
        }

        return view('collaborations.index', compact('collaborations'));
    }

        public function store(Request $request, CollaborationService $service, \App\Services\NotificationService $notifService)
    {
        $user = Auth::user();

        if ($user->hasCompanyRole()) {
            // Company gửi cho University
            $request->validate(['university_id' => 'required|exists:universities,id']);
            $company = $user->active_company;
            $university = \App\Models\University::findOrFail($request->university_id);
            $targetUserId = $university->user_id;
            $senderName = $company->company_name;
        } elseif ($user->hasUniversityRole()) {
            // University gửi cho Company
            $request->validate(['company_id' => 'required|exists:companies,id']);
            $university = $user->active_university;
            $company = \App\Models\Company::findOrFail($request->company_id);
            $targetUserId = $company->user_id;
            $senderName = $university->university_name;
        } else {
            return redirect()->back()->with('error', 'Bạn không có quyền gửi yêu cầu hợp tác.');
        }

        try {
            $service->sendRequest($company, $university);
            $notifService->send($targetUserId, 'Yêu cầu hợp tác mới', "{$senderName} vừa gửi yêu cầu hợp tác cho bạn.", 'info', route('collaborations.index'));
            return redirect()->back()->with('success', 'Đã gửi yêu cầu hợp tác thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

        public function update(Request $request, $id, CollaborationService $service, \App\Services\NotificationService $notifService)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);
        $collaboration = Collaboration::findOrFail($id);
        $user = Auth::user();

        if ($request->status == 'approved') {
            $service->approveRequest($collaboration);
            // Notify initiator
            $targetUserId = $collaboration->initiated_by === 'company' ? $collaboration->company->user_id : $collaboration->university->user_id;
            $approverName = $user->hasCompanyRole() ? $user->active_company->company_name : $user->active_university->university_name;
            $notifService->send($targetUserId, 'Yêu cầu hợp tác đã được duyệt', "{$approverName} đã chấp nhận yêu cầu hợp tác của bạn.", 'success', route('collaborations.index'));
            
            return redirect()->back()->with('success', 'Đã phê duyệt yêu cầu.');
        } else {
            $service->rejectRequest($collaboration);
            // Notify initiator
            $targetUserId = $collaboration->initiated_by === 'company' ? $collaboration->company->user_id : $collaboration->university->user_id;
            $rejecterName = $user->hasCompanyRole() ? $user->active_company->company_name : $user->active_university->university_name;
            $notifService->send($targetUserId, 'Yêu cầu hợp tác bị từ chối', "{$rejecterName} đã từ chối yêu cầu hợp tác của bạn.", 'danger', route('collaborations.index'));

            return redirect()->back()->with('success', 'Đã từ chối yêu cầu.');
        }
    }
}