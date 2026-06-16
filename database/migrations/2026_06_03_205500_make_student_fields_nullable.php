<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable()->change();
            $table->foreignId('major_id')->nullable()->change();
            $table->string('student_code')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('university_id')->nullable(false)->change();
            $table->foreignId('major_id')->nullable(false)->change();
            $table->string('student_code')->nullable(false)->change();
        });
    }
};
