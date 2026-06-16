<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_student_resume_management()
    {
        $response = $this->get('/student/resumes');

        $response->assertRedirect('/login');
    }

    public function test_student_can_view_job_board()
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('Việc làm');
    }

    public function test_student_cannot_apply_to_non_existent_job()
    {
        $user = User::factory()->create(['role' => 'student']);

        // Attempting to apply to a non-existent job ID 999
        $response = $this->actingAs($user)->post('/jobs/999/apply', [
            'resume_id' => 1,
        ]);

        $response->assertStatus(404); // Job model not found throws 404
    }
}
