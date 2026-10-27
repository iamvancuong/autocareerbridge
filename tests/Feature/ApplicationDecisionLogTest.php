<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Field;
use App\Models\Hiring;
use App\Models\Job;
use App\Models\Major;
use App\Models\Notification;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Bảng `applications` chỉ giữ trạng thái mới nhất, nên khi chủ doanh nghiệp chấp
 * nhận một hồ sơ rồi nhân viên tuyển dụng vào từ chối lại, hệ thống không còn dấu
 * vết ai đã đổi. Bộ test khóa lại nhật ký quyết định và việc mỗi lượt quyết định
 * đều phải sinh thông báo cho sinh viên.
 */
class ApplicationDecisionLogTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $recruiter;
    private Application $application;
    private User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->owner = User::factory()->create(['role' => 'company', 'name' => 'Chủ Doanh Nghiệp']);
        $company = Company::create(['user_id' => $this->owner->id, 'company_name' => 'Test Corp']);

        $this->recruiter = User::factory()->create(['role' => 'hiring', 'name' => 'Nhân Viên Tuyển Dụng']);
        Hiring::create([
            'user_id'    => $this->recruiter->id,
            'company_id' => $company->id,
            'position'   => 'Recruiter',
        ]);

        $field = Field::create(['name' => 'Công nghệ thông tin']);
        $major = Major::create(['name' => 'Kỹ thuật phần mềm', 'field_id' => $field->id]);

        $job = Job::create([
            'company_id'   => $company->id,
            'major_id'     => $major->id,
            'title'        => 'Lập trình viên Laravel',
            'description'  => 'Mô tả công việc.',
            'requirements' => 'PHP, Laravel.',
            'is_approved'  => true,
        ]);

        $this->studentUser = User::factory()->create(['role' => 'student']);
        $uniUser = User::factory()->create(['role' => 'university']);
        $university = University::create(['user_id' => $uniUser->id, 'university_name' => 'ĐH Thử Nghiệm']);
        $student = Student::create([
            'user_id'       => $this->studentUser->id,
            'university_id' => $university->id,
            'major_id'      => $major->id,
            'student_code'  => 'SV001',
        ]);

        $this->application = Application::create([
            'job_id'     => $job->id,
            'student_id' => $student->id,
            'status'     => 'pending',
        ]);
    }

    private function decide(User $actor, string $status, string $feedback = '')
    {
        return $this->actingAs($actor)->put(
            route('company.applications.update', $this->application),
            ['status' => $status, 'hr_feedback' => $feedback]
        );
    }

    public function test_each_decision_is_appended_to_the_log(): void
    {
        $this->decide($this->owner, 'accepted', 'Đạt yêu cầu.')->assertRedirect();

        $this->assertCount(1, $this->application->decisions()->get());

        $entry = $this->application->decisions()->first();
        $this->assertSame('accepted', $entry->status);
        $this->assertSame('pending', $entry->previous_status);
        $this->assertSame('company', $entry->decided_by_role);
        $this->assertSame($this->owner->id, $entry->user_id);
    }

    public function test_log_shows_who_overturned_an_earlier_decision(): void
    {
        $this->decide($this->owner, 'accepted', 'Đạt yêu cầu.');
        $this->decide($this->recruiter, 'rejected', 'Vị trí đã tuyển đủ.');

        $entries = $this->application->decisions()->get();

        $this->assertCount(2, $entries, 'Mỗi lượt quyết định phải là một dòng riêng, không ghi đè.');

        // decisions() sắp mới nhất lên đầu.
        $latest = $entries->first();
        $this->assertSame('hiring', $latest->decided_by_role, 'Phải truy được người đổi quyết định là nhân viên tuyển dụng.');
        $this->assertSame('accepted', $latest->previous_status, 'Phải ghi lại trạng thái trước khi bị đổi.');
        $this->assertSame('rejected', $latest->status);

        $this->assertSame('rejected', $this->application->fresh()->status);
    }

    public function test_student_is_notified_on_every_decision(): void
    {
        $this->decide($this->owner, 'accepted');
        $this->decide($this->recruiter, 'rejected');

        $notifications = Notification::where('user_id', $this->studentUser->id)->get();

        $this->assertCount(2, $notifications, 'Mỗi quyết định phải sinh một thông báo trong hệ thống.');
        $this->assertTrue($notifications->contains('type', 'success'));
        $this->assertTrue($notifications->contains('type', 'danger'));
    }

    public function test_decision_log_is_visible_on_the_detail_page(): void
    {
        $this->decide($this->owner, 'accepted', 'Đạt yêu cầu.');

        $this->actingAs($this->owner)
            ->get(route('company.applications.show', $this->application))
            ->assertSuccessful()
            ->assertSee('Lịch sử quyết định tuyển dụng')
            ->assertSee('Chủ doanh nghiệp');
    }
}
