<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Field;
use App\Models\Job;
use App\Models\Major;
use App\Models\Student;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HardenedRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Route này khởi tạo một tiến trình PHPUnit chạy tới 60 giây. Nếu để công
     * khai thì bất kỳ ai cũng có thể gọi liên tục để làm nghẽn máy chủ, nên nó
     * chỉ được đăng ký khi chạy ở môi trường local.
     */
    public function test_test_runner_routes_do_not_exist_outside_local()
    {
        $this->assertFalse(app()->environment('local'));

        $this->post('/api/run-tests')->assertNotFound();
        $this->get('/testing-report')->assertNotFound();
    }

    /**
     * Ràng buộc UNIQUE(job_id, student_id) là chốt chặn cuối cùng cho trường hợp
     * hai request nộp hồ sơ chạy song song cùng vượt qua bước kiểm tra ở tầng PHP.
     */
    public function test_database_rejects_a_duplicate_application()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = $companyUser->company()->create(['company_name' => 'Test Corp']);

        $field = Field::create(['name' => 'Công nghệ thông tin']);
        $major = Major::create(['name' => 'Kỹ thuật phần mềm', 'field_id' => $field->id]);

        $job = Job::create([
            'company_id'   => $company->id,
            'major_id'     => $major->id,
            'title'        => 'Thực tập sinh',
            'description'  => 'Mô tả.',
            'requirements' => 'Yêu cầu.',
            'is_approved'  => true,
        ]);

        $studentUser = User::factory()->create(['role' => 'student']);
        $uniUser = User::factory()->create(['role' => 'university']);
        $university = University::create(['user_id' => $uniUser->id, 'university_name' => 'ĐH Thử Nghiệm']);
        $student = Student::create([
            'user_id'       => $studentUser->id,
            'university_id' => $university->id,
            'major_id'      => $major->id,
            'student_code'  => 'SV001',
        ]);

        Application::create(['job_id' => $job->id, 'student_id' => $student->id, 'status' => 'pending']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Application::create(['job_id' => $job->id, 'student_id' => $student->id, 'status' => 'pending']);
    }
}
