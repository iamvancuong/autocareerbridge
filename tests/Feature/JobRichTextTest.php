<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Major;
use App\Models\Field;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mô tả tin tuyển dụng được soạn bằng CKEditor 5 nên chứa HTML và được in ra
 * bằng {!! !!}. Vì vậy nội dung bắt buộc phải đi qua HTMLPurifier ở tầng máy chủ
 * — không thể tin vào việc trình soạn thảo phía client đã "làm sạch" hộ.
 */
class JobRichTextTest extends TestCase
{
    use RefreshDatabase;

    private function companyUserAndMajor(): array
    {
        $user = User::factory()->create(['role' => 'company']);
        $user->company()->create(['company_name' => 'Test Corp']);

        $field = Field::create(['name' => 'Công nghệ thông tin']);
        $major = Major::create(['name' => 'Kỹ thuật phần mềm', 'field_id' => $field->id]);

        return [$user, $major];
    }

    public function test_malicious_html_is_stripped_before_being_stored()
    {
        [$user, $major] = $this->companyUserAndMajor();

        $this->actingAs($user)->post('/company/jobs', [
            'title'        => 'Lập trình viên PHP',
            'major_id'     => $major->id,
            'description'  => '<p onclick="steal()">Mô tả <strong>hợp lệ</strong></p><script>alert(1)</script>',
            'requirements' => '<img src=x onerror=alert(1)><ul><li>PHP</li></ul>',
        ]);

        $job = Job::first();

        $this->assertNotNull($job);
        // Định dạng hợp lệ được giữ lại...
        $this->assertStringContainsString('<strong>hợp lệ</strong>', $job->description);
        $this->assertStringContainsString('<li>PHP</li>', $job->requirements);
        // ...còn mã độc thì không.
        $this->assertStringNotContainsString('<script', $job->description);
        $this->assertStringNotContainsString('onclick', $job->description);
        $this->assertStringNotContainsString('onerror', $job->requirements);
        $this->assertStringNotContainsString('<img', $job->requirements);
    }

    public function test_editor_content_with_only_whitespace_fails_validation()
    {
        [$user, $major] = $this->companyUserAndMajor();

        // CKEditor gửi lên "<p>&nbsp;</p>" khi người dùng không gõ gì cả.
        $response = $this->actingAs($user)->post('/company/jobs', [
            'title'        => 'Tin rỗng',
            'major_id'     => $major->id,
            'description'  => '<p>&nbsp;</p>',
            'requirements' => '<p>&nbsp;</p>',
        ]);

        $response->assertSessionHasErrors(['description', 'requirements']);
        $this->assertDatabaseCount('jobs', 0);
    }
}
