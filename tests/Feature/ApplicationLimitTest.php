<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Company;
use App\Models\Field;
use App\Models\Job;
use App\Models\Major;
use App\Models\Resume;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Ràng buộc UNIQUE (job_id, student_id) chỉ chặn việc nộp trùng CÙNG một vị trí,
 * không ngăn được sinh viên rải hồ sơ vào hàng loạt vị trí khác nhau — mỗi lượt
 * nộp lại tốn một lần gọi dịch vụ chấm điểm tính phí.
 *
 * Bộ test khóa lại hạn mức hồ sơ đang chờ, đúng như mô tả trong báo cáo:
 * tối đa N hồ sơ ở trạng thái chờ cùng lúc, và hạn mức được hoàn lại ngay khi
 * nhà tuyển dụng đã xử lý xong một hồ sơ.
 */
class ApplicationLimitTest extends TestCase
{
    use RefreshDatabase;

    private Student $student;
    private Resume $resume;
    private Company $company;
    private Major $major;

    protected function setUp(): void
    {
        parent::setUp();

        // Không cấu hình khoá dịch vụ AI: apply() vẫn chạy trọn vẹn, phần chấm điểm
        // ghi ai_score = null kèm lý do nên không phát sinh lời gọi mạng nào.
        config(['services.openai.key' => null]);

        $companyUser = User::factory()->create(['role' => 'company']);
        $this->company = Company::create(['user_id' => $companyUser->id, 'company_name' => 'Test Corp']);

        $field = Field::create(['name' => 'Công nghệ thông tin']);
        $this->major = Major::create(['name' => 'Kỹ thuật phần mềm', 'field_id' => $field->id]);

        $studentUser = User::factory()->create(['role' => 'student']);
        $uniUser = User::factory()->create(['role' => 'university']);
        $university = University::create(['user_id' => $uniUser->id, 'university_name' => 'ĐH Thử Nghiệm']);

        $this->student = Student::create([
            'user_id'       => $studentUser->id,
            'university_id' => $university->id,
            'major_id'      => $this->major->id,
            'student_code'  => 'SV001',
        ]);

        $this->resume = Resume::create([
            'student_id' => $this->student->id,
            'file_path'  => 'resumes/test.pdf',
            'content'    => 'Nội dung CV thử nghiệm.',
            'is_default' => true,
        ]);
    }

    private function makeJob(string $title): Job
    {
        return Job::create([
            'company_id'   => $this->company->id,
            'major_id'     => $this->major->id,
            'title'        => $title,
            'description'  => 'Mô tả công việc.',
            'requirements' => 'PHP, Laravel.',
            'is_approved'  => true,
        ]);
    }

    public function test_student_cannot_exceed_the_pending_application_limit(): void
    {
        config(['recruitment.max_pending_applications' => 3]);
        $service = app(ApplicationService::class);

        for ($i = 1; $i <= 3; $i++) {
            $service->apply($this->student, $this->makeJob("Vị trí {$i}"), $this->resume);
        }

        $this->assertSame(3, Application::where('student_id', $this->student->id)->count());

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/hồ sơ chờ nhà tuyển dụng phản hồi/');

        $service->apply($this->student, $this->makeJob('Vị trí thứ tư'), $this->resume);
    }

    public function test_no_extra_record_is_created_when_the_limit_is_reached(): void
    {
        config(['recruitment.max_pending_applications' => 2]);
        $service = app(ApplicationService::class);

        $service->apply($this->student, $this->makeJob('Vị trí 1'), $this->resume);
        $service->apply($this->student, $this->makeJob('Vị trí 2'), $this->resume);

        try {
            $service->apply($this->student, $this->makeJob('Vị trí 3'), $this->resume);
        } catch (\Exception $e) {
            // Mong đợi bị chặn.
        }

        $this->assertSame(2, Application::where('student_id', $this->student->id)->count(),
            'Hồ sơ vượt hạn mức không được phép ghi vào cơ sở dữ liệu.');
    }

    public function test_quota_is_released_once_the_recruiter_has_decided(): void
    {
        config(['recruitment.max_pending_applications' => 2]);
        $service = app(ApplicationService::class);

        $first = $service->apply($this->student, $this->makeJob('Vị trí 1'), $this->resume);
        $service->apply($this->student, $this->makeJob('Vị trí 2'), $this->resume);

        $this->assertSame(0, $service->remainingQuota($this->student));

        // Nhà tuyển dụng xử lý xong một hồ sơ -> hạn mức phải được hoàn lại ngay.
        $first->update(['status' => 'rejected']);

        $this->assertSame(1, $service->remainingQuota($this->student));

        $third = $service->apply($this->student, $this->makeJob('Vị trí 3'), $this->resume);
        $this->assertNotNull($third->id);
    }

    public function test_limit_is_read_from_configuration(): void
    {
        config(['recruitment.max_pending_applications' => 7]);

        $this->assertSame(7, app(ApplicationService::class)->pendingLimit());
        $this->assertSame(7, app(ApplicationService::class)->remainingQuota($this->student));
    }

    public function test_applying_twice_to_the_same_job_is_still_blocked(): void
    {
        config(['recruitment.max_pending_applications' => 5]);
        $service = app(ApplicationService::class);
        $job = $this->makeJob('Vị trí duy nhất');

        $service->apply($this->student, $job, $this->resume);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bạn đã nộp đơn vào vị trí này rồi.');

        $service->apply($this->student, $job, $this->resume);
    }
}
