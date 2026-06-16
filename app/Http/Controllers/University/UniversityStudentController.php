<?php
namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class UniversityStudentController extends Controller
{
    public function index()
    {
        $university = auth()->user()->active_university;
        if (!$university) abort(403);

        $students = Student::where('university_id', $university->id)
            ->with(['user', 'major'])
            ->withCount(['resumes', 'applications'])
            ->latest()
            ->paginate(15);

        return view('university.students.index', compact('students'));
    }
}
