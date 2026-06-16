<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyJobController;
use App\Http\Controllers\Admin\AdminJobController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCatalogController;
use App\Http\Controllers\UniversityUIController;
use App\Http\Controllers\CompanySearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobUIController;
use App\Http\Controllers\CollaborationUIController;
use App\Http\Controllers\Student\StudentResumeController;
use App\Http\Controllers\Company\CompanyApplicationController;
use App\Http\Controllers\Company\HiringController;
use App\Http\Controllers\Company\CompanyReportController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\University\WorkshopController;
use App\Http\Controllers\University\UniversityMajorController;
use App\Http\Controllers\University\AcademicAffairController;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobUIController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [JobUIController::class, 'show'])->name('jobs.show');
Route::get('/universities', [UniversityUIController::class, 'index'])->name('universities.index');
Route::get('/companies', [CompanySearchController::class, 'index'])->name('companies.index');
Route::get('/workshops', function(\Illuminate\Http\Request $request) {
    $search = $request->input('search');
    $workshops = \App\Models\Workshop::with('university')
        ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
        ->latest()
        ->paginate(9);
    return view('workshops.index', compact('workshops', 'search'));
})->name('workshops.index');

Route::get('/testing-report', function () {
    return view('testing_report');
})->name('testing.report');

Route::post('/api/run-tests', function () {
    // Run PHPUnit using the PHP binary to ensure cross-platform compatibility
    // Using base_path() ensures it runs in the project root, not the public folder
    $process = new \Symfony\Component\Process\Process(
        ['php', 'vendor/phpunit/phpunit/phpunit', '--testdox', '--colors=never'],
        base_path()
    );
    $process->setTimeout(60); // Allow up to 60 seconds
    $process->run();
    
    // Get output (both standard and error to ensure we don't miss anything)
    $output = $process->getOutput() . "\n" . $process->getErrorOutput();
    
    $testsCount = 0; $passedCount = 0; $failedCount = 0;
    
    // Parse output
    if (preg_match('/Tests: (\d+).*?Failures: (\d+)/s', $output, $matches)) {
        $testsCount = (int)$matches[1];
        $failedCount = (int)$matches[2];
        $passedCount = $testsCount - $failedCount;
    } elseif (preg_match('/OK \((\d+) tests/s', $output, $matches)) {
        $testsCount = (int)$matches[1];
        $passedCount = $testsCount;
        $failedCount = 0;
    }

    // Default fallback if parsing fails but output exists
    if ($testsCount === 0 && strpos($output, 'PASS') !== false) {
        $testsCount = substr_count($output, 'PASS');
        $passedCount = $testsCount;
    }

    return response()->json([
        'output' => $output ?: 'No output received from PHPUnit. Check configuration.',
        'testsCount' => $testsCount,
        'passedCount' => $passedCount,
        'failedCount' => $failedCount,
        'time' => now()->format('H:i:s d/m/Y')
    ]);
})->name('api.run.tests');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/mock-login/{role}', [AuthController::class, 'mockLogin'])->name('mock.login');
Route::get('/mock-logout', [AuthController::class, 'mockLogout'])->name('mock.logout');

// Authenticated
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

        // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Jobs Apply
    Route::post('/jobs/{id}/apply', [JobUIController::class, 'apply'])->name('jobs.apply');

    // Collaborations
    Route::get('/collaborations', [CollaborationUIController::class, 'index'])->name('collaborations.index');
    Route::post('/collaborations', [CollaborationUIController::class, 'store'])->name('collaborations.store');
    Route::put('/collaborations/{id}', [CollaborationUIController::class, 'update'])->name('collaborations.update');

    // Student
    Route::prefix('student')->name('student.')->group(function () {
        Route::resource('resumes', StudentResumeController::class)->only(['index', 'store', 'destroy']);
        Route::put('resumes/{resume}/set-default', [StudentResumeController::class, 'setDefault'])->name('resumes.setDefault');
    });

    // Company
    Route::prefix('company')->name('company.')->group(function () {
        Route::resource('jobs', CompanyJobController::class);
        Route::resource('applications', CompanyApplicationController::class)->only(['index', 'show', 'update']);
        Route::resource('hirings', HiringController::class)->only(['index', 'store', 'destroy']);
        Route::get('reports', [CompanyReportController::class, 'index'])->name('reports.index');
    });

    // University
    Route::prefix('university')->name('university.')->group(function () {
        Route::resource('workshops', WorkshopController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::resource('academic_affairs', AcademicAffairController::class)->only(['index', 'store', 'destroy']);
        Route::get('majors', [UniversityMajorController::class, 'index'])->name('majors.index');
        Route::post('majors', [UniversityMajorController::class, 'store'])->name('majors.store');
        Route::get('students', [\App\Http\Controllers\University\UniversityStudentController::class, 'index'])->name('students.index');
        Route::get('reports', [\App\Http\Controllers\University\UniversityReportController::class, 'index'])->name('reports.index');
    });

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('jobs', [AdminJobController::class, 'index'])->name('jobs.index');
        Route::post('jobs/{job}/approve', [AdminJobController::class, 'approve'])->name('jobs.approve');
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
        Route::get('catalog', [AdminCatalogController::class, 'fields'])->name('catalog.index');
        Route::post('catalog/fields', [AdminCatalogController::class, 'storeField'])->name('catalog.fields.store');
        Route::delete('catalog/fields/{field}', [AdminCatalogController::class, 'destroyField'])->name('catalog.fields.destroy');
        Route::post('catalog/majors', [AdminCatalogController::class, 'storeMajor'])->name('catalog.majors.store');
        Route::delete('catalog/majors/{major}', [AdminCatalogController::class, 'destroyMajor'])->name('catalog.majors.destroy');
    });
});