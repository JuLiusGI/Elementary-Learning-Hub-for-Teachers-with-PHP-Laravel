<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['head_teacher', 'teacher'])->default('teacher')->after('password');
            $table->enum('grade_level', [
                'kinder', 'grade_1', 'grade_2', 'grade_3',
                'grade_4', 'grade_5', 'grade_6',
            ])->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('grade_level');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            $table->index('role');
            $table->index('grade_level');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['grade_level']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['role', 'grade_level', 'is_active', 'last_login_at']);
        });
    }
};
