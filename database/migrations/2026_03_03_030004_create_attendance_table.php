<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->time('time_in')->nullable()->comment('For late tracking');
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users');

            // Offline sync tracking
            $table->string('client_id', 36)->nullable()->comment('UUID from client for dedup');
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'date'], 'unique_student_date');
            $table->index('date', 'idx_attendance_date');
            $table->index('status', 'idx_attendance_status');
            $table->index('synced_at', 'idx_attendance_synced');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
