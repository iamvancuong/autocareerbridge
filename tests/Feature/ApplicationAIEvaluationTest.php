<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Field;
use App\Models\Job;
use App\Models\Resume;
use App\Models\Student;
use App\Models\User;
use App\Models\Company;
use App\Models\Major;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ApplicationAIEvaluationTest extends TestCase
{
    use RefreshDatabase;

    private function setupBasicData()
    {
        $companyUser = User::factory()->create(['role' => 'company', 'is_active' => true]);
        $company = Company::create(['user_id' => $companyUser->id, 'company_name' => 'Test Corp']);

        $field = Field::create(['name' => 'Information Technology']);
        $major = Major::create(['name' => 'Software Engineering', 'field_id' => $field->id]);

        $studentUser = User::factory()->create(['role' => 'student', 'is_active' => true]);
        $student = Student::create(['user_id' => $studentUser->id, 'major_id' => $major->id]);

        $job = Job::create([
            'company_id' => $company->id,
            'major_id' => $major->id,
            'title' => 'Software Engineer',
            'description' => 'Test job',
            'requirements' => 'PHP, Laravel',
            'is_approved' => true,
        ]);

        $resume = Resume::create([
            'student_id' => $student->id,
            'file_path' => 'test.pdf',
            'original_name' => 'CV.pdf',
            'content' => 'Test resume content',
        ]);

        return compact('student', 'job', 'resume', 'company');
    }

    public function test_application_sets_ai_score_null_when_openai_key_missing()
    {
        // Không set OPENAI_API_KEY, giả lập lỗi cấu hình.
        config(['services.openai.key' => null]);

        ['student' => $student, 'job' => $job, 'resume' => $resume] = $this->setupBasicData();

        $service = new ApplicationService();
        $application = $service->apply($student, $job, $resume);

        // Fix 3: Ngoài ai_score = null (chứ không phải điểm ngẫu nhiên),
        // ai_review phải giải thích rõ tại sao.
        $this->assertNull($application->ai_score);
        $this->assertNotNull($application->ai_review);
        $this->assertStringContainsString('Chưa có đánh giá', $application->ai_review);
        $this->assertStringContainsString('API key', $application->ai_review);
    }

    public function test_application_review_explains_why_no_score()
    {
        // Khi không chấm được điểm, ai_review phải giải thích lý do,
        // chứ không phải một nhận xét giả lập nói "điểm được đánh giá ngẫu nhiên".
        config(['services.openai.key' => null]);

        ['student' => $student, 'job' => $job, 'resume' => $resume] = $this->setupBasicData();

        $service = new ApplicationService();
        $application = $service->apply($student, $job, $resume);

        // ai_review phải giải thích rõ: không phải nhận xét giả lập.
        $this->assertNotNull($application->ai_review);
        $this->assertStringNotContainsString('ngẫu nhiên', $application->ai_review);
        $this->assertStringContainsString('Chưa có', $application->ai_review);
    }
}
