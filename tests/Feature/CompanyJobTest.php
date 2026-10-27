<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_company_job_management()
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->get('/company/jobs');

        $response->assertForbidden(); // Middleware role:company,hiring chặn student
    }

    public function test_company_can_access_job_creation_page()
    {
        $user = User::factory()->create(['role' => 'company']);
        $user->company()->create(['company_name' => 'Test Corp', 'is_verified' => true]);

        $response = $this->actingAs($user)->get('/company/jobs/create');

        $response->assertStatus(200);
        $response->assertSee('Đăng Tin Tuyển Dụng Mới');
    }

    public function test_company_cannot_create_job_without_required_fields()
    {
        $user = User::factory()->create(['role' => 'company']);
        $user->company()->create(['company_name' => 'Test Corp', 'is_verified' => true]);

        $response = $this->actingAs($user)->post('/company/jobs', [
            'title' => '', // Missing title
            'description' => 'A great job',
        ]);

        $response->assertSessionHasErrors('title');
    }
}
