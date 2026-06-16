<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Collaboration;
use Illuminate\Http\Request;

class CompanySearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Company::query();
        if ($search) $query->where('company_name', 'like', "%{$search}%");
        $companies = $query->get();

        $universityId = auth()->check() && auth()->user()->active_university ? auth()->user()->active_university->id : 0;
        $collabStatuses = [];
        if ($universityId) {
            Collaboration::where('university_id', $universityId)->get()->each(function($c) use (&$collabStatuses) {
                $collabStatuses[$c->company_id] = $c->status;
            });
        }
        return view('companies.index', compact('companies', 'search', 'collabStatuses'));
    }
}