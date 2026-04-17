<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('school_year_id')->constrained('school_years');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);

            // Written Work (WW) - 40%
            $table->decimal('ww_total_score', 8, 2)->nullable()->comment('Sum of all WW scores');
            $table->decimal('ww_max_score', 8, 2)->nullable()->comment('Sum of all WW max scores');
            $table->decimal('ww_percentage', 5, 2)->nullable()->comment('(total/max) * 100');
            $table->decimal('ww_weighted', 5, 2)->nullable()->comment('percentage * 0.40');

            // Performance Task (PT) - 40%
            $table->decimal('pt_total_score', 8, 2)->nullable();
            $table->decimal('pt_max_score', 8, 2)->nullable();
            $table->decimal('pt_percentage', 5, 2)->nullable();
            $table->decimal('pt_weighted', 5, 2)->nullable()->comment('percentage * 0.40');

            // Quarterly Assessment (QA) - 20%
            $table->decimal('qa_score', 8, 2)->nullable();
            $table->decimal('qa_max_score', 8, 2)->nullable();
            $table->decimal('qa_percentage', 5, 2)->nullable();
            $table->decimal('qa_weighted', 5, 2)->nullable()->comment('percentage * 0.20');

            // Final Quarterly Grade
            $table->decimal('quarterly_grade', 5, 2)->nullable()->comment('ww_weighted + pt_weighted + qa_weighted');
            $table->string('remarks', 50)->nullable()->comment('Passed, Failed, etc.');

            // Approval workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'locked'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Offline sync
            $table->string('client_id', 36)->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'subject_id', 'school_year_id', 'quarter'], 'unique_student_subject_quarter');
            $table->index('status', 'idx_grades_status');
            $table->index('quarter', 'idx_grades_quarter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
