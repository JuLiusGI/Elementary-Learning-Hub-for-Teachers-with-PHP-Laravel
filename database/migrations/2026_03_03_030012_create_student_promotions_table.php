<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('from_school_year_id')->constrained('school_years');
            $table->foreignId('to_school_year_id')->constrained('school_years');
            $table->enum('from_grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6']);
            $table->enum('to_grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6', 'graduated']);
            $table->decimal('general_average', 5, 2)->nullable();
            $table->enum('status', ['promoted', 'retained', 'graduated']);
            $table->foreignId('decision_by')->constrained('users')->comment('Head Teacher');
            $table->text('remarks')->nullable();
            $table->timestamp('promoted_at');
            $table->timestamps();

            $table->index('student_id', 'idx_promotions_student');
            $table->index('from_school_year_id', 'idx_promotions_school_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
    }
};
