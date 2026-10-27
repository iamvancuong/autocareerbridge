<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trước đây việc chặn nộp trùng chỉ dựa vào một câu truy vấn kiểm tra ở tầng PHP.
 * Nếu sinh viên bấm "Ứng tuyển" hai lần thật nhanh, hai request chạy song song
 * đều thấy "chưa có đơn nào" và cùng ghi vào CSDL. Ràng buộc UNIQUE ở tầng CSDL
 * là chốt chặn cuối cùng cho trường hợp đó.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unique(['job_id', 'student_id'], 'applications_job_student_unique');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropUnique('applications_job_student_unique');
        });
    }
};
