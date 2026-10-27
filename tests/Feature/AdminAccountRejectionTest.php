<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Trước đây thao tác "Từ chối" chỉ gán lại is_active = false, trong khi tài khoản
 * chờ duyệt vốn đã mang giá trị đó. Hệ quả là quản trị viên bấm xong vẫn thấy
 * tài khoản nằm nguyên trong hàng chờ, duyệt mãi không hết.
 *
 * Bộ test này khóa lại hành vi đúng: bị từ chối thì rời hàng chờ, nhưng bản ghi
 * vẫn còn để truy vết và vẫn có thể được duyệt lại.
 */
class AdminAccountRejectionTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function pendingCompany(): User
    {
        return User::factory()->create([
            'role'      => 'company',
            'is_active' => false,
        ]);
    }

    public function test_rejected_account_leaves_the_pending_queue(): void
    {
        $pending = $this->pendingCompany();

        $this->actingAs($this->admin())
            ->post(route('admin.users.reject', $pending))
            ->assertRedirect();

        $pending->refresh();

        $this->assertNotNull($pending->rejected_at, 'Phải ghi lại mốc thời gian từ chối.');
        $this->assertFalse($pending->is_active, 'Tài khoản bị từ chối vẫn không được kích hoạt.');
        // Bản ghi phải được giữ lại để còn truy vết, không bị xóa đi.
        $this->assertDatabaseHas('users', ['id' => $pending->id]);
    }

    public function test_pending_list_no_longer_shows_a_rejected_account(): void
    {
        $rejected = $this->pendingCompany();
        $stillWaiting = $this->pendingCompany();

        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.users.reject', $rejected));

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $pendingUsers = $response->viewData('pendingUsers');

        $this->assertFalse($pendingUsers->contains('id', $rejected->id), 'Tài khoản đã từ chối không được nằm trong hàng chờ.');
        $this->assertTrue($pendingUsers->contains('id', $stillWaiting->id), 'Tài khoản chưa xét vẫn phải nằm trong hàng chờ.');
    }

    public function test_admin_can_reverse_a_rejection_by_approving(): void
    {
        $user = $this->pendingCompany();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.reject', $user));
        $this->actingAs($admin)->post(route('admin.users.approve', $user));

        $user->refresh();

        $this->assertTrue($user->is_active);
        $this->assertNull($user->rejected_at, 'Duyệt lại phải xóa dấu từ chối cũ.');
    }
}
