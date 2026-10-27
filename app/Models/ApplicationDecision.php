<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Một dòng trong nhật ký quyết định tuyển dụng. Chỉ ghi thêm, không sửa/xóa.
 */
class ApplicationDecision extends Model {
    protected $fillable = [
        'application_id', 'user_id', 'decided_by_role', 'decided_by_name',
        'previous_status', 'status', 'hr_feedback',
    ];

    public function application() { return $this->belongsTo(Application::class); }
    public function user() { return $this->belongsTo(User::class); }

    /** Nhãn tiếng Việt của vai trò người ra quyết định, dùng để hiển thị. */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->decided_by_role) {
            'company' => 'Chủ doanh nghiệp',
            'hiring'  => 'Nhân viên tuyển dụng',
            default   => $this->decided_by_role ?? 'Không rõ',
        };
    }
}
