<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('lrn', 12)->unique()->comment('Learner Reference Number');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix', 20)->nullable()->comment('Jr., III, etc.');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);

            // Address
            $table->string('address_street', 255)->nullable();
            $table->string('address_barangay', 255)->nullable();
            $table->string('address_municipality', 255)->nullable();
            $table->string('address_province', 255)->nullable();

            // Guardian Information
            $table->string('guardian_name', 255);
            $table->string('guardian_contact', 20)->nullable();
            $table->enum('guardian_relationship', ['mother', 'father', 'guardian', 'grandparent', 'other'])->default('guardian');

            // Additional Info
            $table->text('special_needs')->nullable();
            $table->text('medical_notes')->nullable();
            $table->string('photo_path', 255)->nullable();

            // Academic Info
            $table->enum('grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6']);
            $table->foreignId('school_year_id')->constrained('school_years');
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('enrollment_status', ['active', 'transferred', 'dropped', 'graduated'])->default('active');
            $table->date('date_enrolled');
            $table->string('previous_school', 255)->nullable();
            $table->date('transfer_date')->nullable();
            $table->text('transfer_reason')->nullable();

            $table->timestamps();

            $table->index('grade_level', 'idx_students_grade_level');
            $table->index('school_year_id', 'idx_students_school_year');
            $table->index('teacher_id', 'idx_students_teacher');
            $table->index('enrollment_status', 'idx_students_status');
            $table->index(['last_name', 'first_name'], 'idx_students_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
