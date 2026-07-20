<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->string('initiated_by')->nullable()->after('status');
        });

        Schema::table('workshops', function (Blueprint $table) {
            $table->date('date')->nullable()->after('description');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type')->nullable()->after('message');
            $table->string('url')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn('initiated_by');
        });

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'url']);
        });
    }
};
