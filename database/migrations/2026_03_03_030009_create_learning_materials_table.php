<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('subject_id')->constrained('subjects');
            $table->enum('grade_level', ['kinder', 'grade_1', 'grade_2', 'grade_3', 'grade_4', 'grade_5', 'grade_6']);
            $table->enum('quarter', ['Q1', 'Q2', 'Q3', 'Q4']);
            $table->tinyInteger('week_number')->unsigned()->nullable();

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('file_type', ['pdf', 'image', 'video', 'link']);
            $table->string('file_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->bigInteger('file_size')->unsigned()->nullable()->comment('in bytes');
            $table->string('mime_type', 100)->nullable();

            $table->boolean('is_downloadable')->default(true);
            $table->integer('download_count')->unsigned()->default(0);

            $table->timestamps();

            $table->index('grade_level', 'idx_materials_grade');
            $table->index('subject_id', 'idx_materials_subject');
            $table->index('quarter', 'idx_materials_quarter');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_materials');
    }
};
