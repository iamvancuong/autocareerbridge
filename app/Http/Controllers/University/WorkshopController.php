<?php
namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index()
    {
        $university = auth()->user()->active_university;
        $workshops = $university ? Workshop::where('university_id', $university->id)->latest()->get() : collect();
        return view('university.workshops.index', compact('workshops'));
    }

    public function create() { return view('university.workshops.create'); }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);
        $university = auth()->user()->active_university;
        Workshop::create([
            'university_id' => $university->id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
        ]);
        return redirect()->route('university.workshops.index')->with('success', 'Đã tạo Workshop!');
    }

    public function destroy(Workshop $workshop)
    {
        if ($workshop->university_id !== auth()->user()->active_university->id) abort(403);
        $workshop->delete();
        return redirect()->back()->with('success', 'Đã xóa Workshop.');
    }
}