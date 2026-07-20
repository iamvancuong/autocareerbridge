<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    public function test_guest_is_redirected_away_from_admin_area()
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_dashboard()
    {
        $this->actingAs($this->userWithRole('student'))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_student_cannot_approve_users()
    {
        $target = $this->userWithRole('company');

        $this->actingAs($this->userWithRole('student'))
            ->post("/admin/users/{$target->id}/approve")
            ->assertForbidden();
    }

    public function test_company_cannot_access_admin_area()
    {
        $this->actingAs($this->userWithRole('company'))
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin')
            ->assertSuccessful();
    }

    public function test_student_cannot_access_company_area()
    {
        $this->actingAs($this->userWithRole('student'))
            ->get('/company/jobs')
            ->assertForbidden();
    }

    public function test_company_cannot_access_university_area()
    {
        $this->actingAs($this->userWithRole('company'))
            ->get('/university/students')
            ->assertForbidden();
    }

    public function test_hiring_role_can_still_access_company_area()
    {
        // Role phụ 'hiring' phải đi kèm bản ghi Hiring trỏ về Company,
        // vì User::getActiveCompanyAttribute() resolve công ty qua quan hệ này.
        $owner = $this->userWithRole('company');
        $company = \App\Models\Company::create([
            'user_id' => $owner->id,
            'company_name' => 'Test Corp',
        ]);

        $hr = $this->userWithRole('hiring');
        \App\Models\Hiring::create([
            'user_id' => $hr->id,
            'company_id' => $company->id,
            'position' => 'Recruiter',
        ]);

        $this->actingAs($hr)
            ->get('/company/jobs')
            ->assertSuccessful();
    }

    public function test_mock_login_route_does_not_exist_outside_local()
    {
        // Bộ test chạy với APP_ENV=testing, giống môi trường production.
        $this->get('/mock-login/admin')->assertNotFound();
        $this->assertGuest();
    }
}
