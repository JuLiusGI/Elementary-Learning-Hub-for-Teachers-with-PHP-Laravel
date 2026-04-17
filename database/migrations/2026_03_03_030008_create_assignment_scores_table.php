<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id'], 'unique_assignment_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_scores');
    }
};
