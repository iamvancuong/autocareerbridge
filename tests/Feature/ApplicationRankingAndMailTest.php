<?php

namespace Tests\Feature;

use App\Mail\AccountApprovedMail;
use App\Mail\ApplicationStatusMail;
use App\Models\Application;
use App\Models\Field;
use App\Models\Job;
use App\Models\Major;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Kiểm chứng hai hành vi mà báo cáo có nêu:
 *  - Danh sách ứng viên được xếp hạng theo điểm AI giảm dần (mục 2.3.1);
 *  - Kết quả xét duyệt được gửi qua email chứ không chỉ nằm trong hệ thống (mục 3.2).
 */
class ApplicationRankingAndMailTest extends TestCase
{
    use RefreshDatabase;

    private User $companyUser;
    private Job $job;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyUser = User::factory()->create(['role' => 'company']);
        $company = $this->companyUser->company()->create(['company_name' => 'Test Corp']);

        $field = Field::create(['name' => 'Công nghệ thông tin']);
        $major = Major::create(['name' => 'Kỹ thuật phần mềm', 'field_id' => $field->id]);

        $this->job = Job::create([
            'company_id'   => $company->id,
            'major_id'     => $major->id,
            'title'        => 'Lập trình viên Laravel',
            'description'  => 'Mô tả công việc.',
            'requirements' => 'PHP, Laravel.',
            'is_approved'  => true,
        ]);
    }

    private function applicationWithScore(string $studentName, ?int $score): Application
    {
        $user = User::factory()->create(['role' => 'student', 'name' => $studentName]);
        $uniUser = User::factory()->create(['role' => 'university']);
        $university = University::create(['user_id' => $uniUser->id, 'university_name' => 'ĐH Thử Nghiệm']);

        $student = Student::create([
            'user_id'       => $user->id,
            'university_id' => $university->id,
            'major_id'      => $this->job->major_id,
            'student_code'  => 'SV' . $user->id,
        ]);

        return Application::create([
            'job_id'     => $this->job->id,
            'student_id' => $student->id,
            'status'     => 'pending',
            'ai_score'   => $score,
        ]);
    }

    public function test_applications_are_ranked_by_ai_score_with_unscored_last()
    {
        $this->applicationWithScore('Ung vien diem thap', 40);
        $this->applicationWithScore('Ung vien chua cham', null);
        $this->applicationWithScore('Ung vien diem cao', 92);

        $response = $this->actingAs($this->companyUser)->get('/company/applications');

        $response->assertOk();
        $scores = $response->viewData('applications')->pluck('ai_score')->all();

        // Điểm cao trước, và hồ sơ chưa chấm (null) bị đẩy xuống cuối để không
        // bị nhầm là ứng viên yếu.
        $this->assertSame([92, 40, null], $scores);
    }

    public function test_newest_sort_mode_ignores_ai_score()
    {
        $this->applicationWithScore('Nop truoc', 92);
        $this->applicationWithScore('Nop sau', 40);

        $response = $this->actingAs($this->companyUser)->get('/company/applications?sort=newest');

        $response->assertOk();
        $this->assertSame([40, 92], $response->viewData('applications')->pluck('ai_score')->all());
    }

    public function test_statistics_count_every_application_not_just_the_current_page()
    {
        // Trang danh sách phân trang 10 bản ghi mỗi trang.
        foreach (range(1, 12) as $i) {
            $this->applicationWithScore("Ung vien {$i}", $i);
        }

        $response = $this->actingAs($this->companyUser)->get('/company/applications');

        $response->assertOk();
        $this->assertSame(12, $response->viewData('stats')['total']);
        $this->assertSame(12, $response->viewData('stats')['pending']);
    }

    public function test_student_receives_an_email_when_application_is_decided()
    {
        Mail::fake();

        $application = $this->applicationWithScore('Ung vien A', 88);

        $this->actingAs($this->companyUser)->put("/company/applications/{$application->id}", [
            'status'      => 'accepted',
            'hr_feedback' => 'Hồ sơ rất phù hợp.',
        ]);

        Mail::assertSent(ApplicationStatusMail::class, function ($mail) use ($application) {
            return $mail->hasTo($application->student->user->email);
        });
    }

    public function test_organisation_receives_an_email_when_admin_approves_the_account()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $pending = User::factory()->create(['role' => 'company', 'is_active' => false]);

        $this->actingAs($admin)->post("/admin/users/{$pending->id}/approve");

        $this->assertTrue($pending->fresh()->is_active);
        Mail::assertSent(AccountApprovedMail::class, fn($mail) => $mail->hasTo($pending->email));
    }
}
