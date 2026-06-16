<?php
namespace App\Http\Controllers;

use App\Models\University;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniversityUIController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = University::with('majors');
        if ($search) {
            $query->where('university_name', 'like', "%{$search}%");
        }
        $universities = $query->get();

        $companyId = auth()->check() && auth()->user()->hasCompanyRole() ? auth()->user()->active_company->id ?? 0 : 0;
        
        // Get current collaboration statuses for the logged in company
        $collabStatuses = [];
        if ($companyId) {
            $collabs = Collaboration::where('company_id', $companyId)->get();
            foreach ($collabs as $collab) {
                $collabStatuses[$collab->university_id] = $collab->status; // pending, approved, rejected
            }
        }

        return view('universities.index', compact('universities', 'search', 'collabStatuses'));
    }
}