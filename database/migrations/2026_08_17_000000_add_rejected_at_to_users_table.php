<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trước đây "Từ chối" chỉ gán lại is_active = false, nhưng tài khoản đang chờ
 * duyệt vốn đã là false nên thao tác này không đổi gì: tài khoản vẫn nằm nguyên
 * trong hàng chờ và quản trị viên duyệt mãi không hết.
 *
 * Cần một cột riêng để phân biệt "chưa xét" với "đã xét và bị từ chối" — không
 * thể suy ra được từ mỗi cờ is_active.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('rejected_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rejected_at');
        });
    }
};
