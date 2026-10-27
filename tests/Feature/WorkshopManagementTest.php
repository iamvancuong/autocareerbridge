<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Trước đây bảng `workshops` để `company_id` và `scheduled_at` ở dạng NOT NULL
 * trong khi luồng tạo mới của Nhà trường không truyền hai giá trị này, khiến mọi
 * lần bấm "Tạo Workshop" đều đổ lỗi ràng buộc CSDL. Các bài test dưới đây khoá
 * lại hành vi đúng để lỗi không tái diễn.
 */
class WorkshopManagementTest extends TestCase
{
    use RefreshDatabase;

    private function universityUser(): User
    {
        $user = User::factory()->create(['role' => 'university']);
        $user->university()->create(['university_name' => 'Đại học Thử Nghiệm']);

        return $user;
    }

    public function test_university_can_create_workshop_without_a_partner_company()
    {
        $user = $this->universityUser();

        $response = $this->actingAs($user)->post('/university/workshops', [
            'title'       => 'Ngày hội việc làm 2026',
            'description' => 'Sự kiện kết nối sinh viên và doanh nghiệp.',
            'date'        => '2026-09-01',
        ]);

        $response->assertRedirect(route('university.workshops.index'));
        $this->assertDatabaseHas('workshops', [
            'title'         => 'Ngày hội việc làm 2026',
            'university_id' => $user->university->id,
            'company_id'    => null,
        ]);
    }

    public function test_created_workshop_keeps_scheduled_at_in_sync_with_date()
    {
        $user = $this->universityUser();

        $this->actingAs($user)->post('/university/workshops', [
            'title'       => 'Hội thảo hướng nghiệp',
            'description' => 'Chia sẻ kinh nghiệm phỏng vấn.',
            'date'        => '2026-10-15',
        ]);

        $workshop = Workshop::first();

        $this->assertNotNull($workshop->scheduled_at);
        $this->assertSame('2026-10-15', $workshop->date->toDateString());
        $this->assertSame('2026-10-15', $workshop->scheduled_at->toDateString());
    }

    public function test_workshop_requires_a_date()
    {
        $user = $this->universityUser();

        $response = $this->actingAs($user)->post('/university/workshops', [
            'title'       => 'Thiếu ngày tổ chức',
            'description' => 'Mô tả hợp lệ.',
        ]);

        $response->assertSessionHasErrors('date');
        $this->assertDatabaseCount('workshops', 0);
    }
}
