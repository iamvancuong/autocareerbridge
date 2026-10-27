<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\University;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /* ---- Mock helpers (kept for dev testing) ---- */
    public function mockLogin($role)
    {
        // Chặn tuyệt đối ngoài môi trường local: đây là đăng nhập không cần mật khẩu.
        abort_unless(app()->environment('local'), 404);

        $user = User::where('role', $role)->first();
        if ($user) { Auth::login($user); return redirect()->route('dashboard'); }
        return redirect()->route('home')->with('error', 'Không tìm thấy user role ' . $role);
    }
    public function mockLogout() { Auth::logout(); return redirect()->route('home'); }

    /* ---- Real Auth ---- */
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Tài khoản của bạn đang chờ Admin phê duyệt.');
            }
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }
        return back()->with('error', 'Email hoặc mật khẩu không đúng.')->withInput();
    }

    public function logout(Request $request) { Auth::logout(); $request->session()->invalidate(); return redirect()->route('home'); }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:company,university,student',
        ]);

        $isActive = $request->role === 'student'; // Students are active immediately, companies/universities need approval

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => $isActive,
        ]);

        // Create associated profile
        if ($request->role === 'company') {
            Company::create(['user_id' => $user->id, 'company_name' => $request->name]);
        } elseif ($request->role === 'university') {
            University::create(['user_id' => $user->id, 'university_name' => $request->name]);
        } elseif ($request->role === 'student') {
            Student::create(['user_id' => $user->id]);
        }

        if ($isActive) {
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Đăng ký thành công!');
        }
        return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng chờ Admin phê duyệt tài khoản của bạn.');
    }
}