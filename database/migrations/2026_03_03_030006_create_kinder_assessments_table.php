<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinder_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years');
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->enum('domain', ['socio_emotional', 'language', 'cognitive', 'physical', 'creative']);
            $table->enum('rating', ['beginning', 'developing', 'proficient'])->nullable();
            $table->text('remarks')->nullable();

            // Approval workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'locked'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Offline sync
            $table->string('client_id', 36)->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->unique(['student_id', 'school_year_id', 'quarter', 'domain'], 'unique_kinder_assessment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinder_assessments');
    }
};
