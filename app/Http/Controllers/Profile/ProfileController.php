<?php
namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $profile = match($user->role) {
            'company' => $user->active_company,
            'university' => $user->active_university,
            'student' => $user->student,
            default => null,
        };

        $universities = [];
        $majors = [];
        if ($user->role === 'student') {
            $universities = \App\Models\University::all();
            $majors = \App\Models\Major::all();
        }

        return view('profile.show', compact('user', 'profile', 'universities', 'majors'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user->update(['name' => $request->name]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }

        if ($user->hasCompanyRole() && $user->active_company) {
            $user->active_company->update([
                'company_name' => $request->input('org_name', $user->active_company->company_name),
                'description' => $request->input('description'),
                'address' => $request->input('address'),
            ]);
        } elseif ($user->hasUniversityRole() && $user->active_university) {
            $user->active_university->update([
                'university_name' => $request->input('org_name', $user->active_university->university_name),
                'description' => $request->input('description'),
                'address' => $request->input('address'),
            ]);
        } elseif ($user->role === 'student') {
            \App\Models\Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'university_id' => $request->input('university_id'),
                    'major_id' => $request->input('major_id'),
                    'student_code' => $request->input('student_code'),
                    'gpa' => $request->input('gpa'),
                ]
            );
        }

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }
}