<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nhật ký quyết định tuyển dụng.
 *
 * Bảng `applications` chỉ giữ được trạng thái MỚI NHẤT, nên khi chủ doanh nghiệp
 * chấp nhận một hồ sơ rồi nhân viên tuyển dụng vào từ chối lại, hệ thống không
 * còn dấu vết nào cho biết ai đã đổi, đổi lúc nào và đổi từ trạng thái gì.
 *
 * Mỗi lần bấm Chấp nhận / Từ chối sinh đúng một dòng ở đây, chỉ ghi thêm chứ
 * không sửa hay xóa, để doanh nghiệp truy được trách nhiệm về từng quyết định.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_decisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            // Giữ lại nhật ký kể cả khi tài khoản người quyết định bị gỡ.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Chụp lại vai trò tại thời điểm quyết định: vai trò của một tài khoản
            // có thể đổi về sau, nhưng nhật ký phải phản ánh đúng lúc đó.
            $table->string('decided_by_role')->nullable();
            $table->string('decided_by_name')->nullable();

            $table->string('previous_status')->nullable();
            $table->string('status');
            $table->text('hr_feedback')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_decisions');
    }
};
