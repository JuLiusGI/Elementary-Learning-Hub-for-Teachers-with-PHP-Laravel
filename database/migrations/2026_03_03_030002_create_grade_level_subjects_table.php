<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_level_subjects', function (Blueprint $table) {
            $table->id();
            $table->enum('grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6']);
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->tinyInteger('display_order')->unsigned()->default(0);
            $table->timestamps();

            $table->unique(['grade_level', 'subject_id'], 'unique_grade_subject');
            $table->index('grade_level', 'idx_grade_level_subjects_grade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_level_subjects');
    }
};
