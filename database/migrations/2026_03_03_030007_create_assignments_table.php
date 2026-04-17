<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->enum('grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6']);
            $table->foreignId('school_year_id')->constrained('school_years');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('type', ['written_work', 'performance_task']);
            $table->decimal('max_score', 8, 2);
            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->index('teacher_id', 'idx_assignments_teacher');
            $table->index('grade_level', 'idx_assignments_grade');
            $table->index('quarter', 'idx_assignments_quarter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
