<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Workshop do Nhà trường tự tổ chức nên chưa chắc đã có doanh nghiệp đồng hành,
 * nhưng `company_id` lại đang NOT NULL khiến mọi lần tạo workshop đều lỗi
 * "NOT NULL constraint failed: workshops.company_id".
 * Cột `scheduled_at` cũng bị bỏ trống vì luồng tạo mới chỉ nhập `date`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->change();
            $table->dateTime('scheduled_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
            $table->dateTime('scheduled_at')->nullable(false)->change();
        });
    }
};
