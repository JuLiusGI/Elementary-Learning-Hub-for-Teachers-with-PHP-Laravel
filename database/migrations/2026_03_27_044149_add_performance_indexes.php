<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kinder_assessments', function (Blueprint $table) {
            $table->index(['school_year_id', 'quarter', 'domain'], 'kinder_sy_quarter_domain');
            $table->index('status', 'kinder_status');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->index(['school_year_id', 'subject_id', 'quarter'], 'grades_sy_subject_quarter');
        });

        Schema::table('learning_materials', function (Blueprint $table) {
            $table->index('uploaded_by', 'materials_uploaded_by');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['published_at', 'expires_at'], 'announcements_published_expires');
            $table->index('priority', 'announcements_priority');
        });
    }

    public function down(): void
    {
        Schema::table('kinder_assessments', function (Blueprint $table) {
            $table->dropIndex('kinder_sy_quarter_domain');
            $table->dropIndex('kinder_status');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('grades_sy_subject_quarter');
        });

        Schema::table('learning_materials', function (Blueprint $table) {
            $table->dropIndex('materials_uploaded_by');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_published_expires');
            $table->dropIndex('announcements_priority');
        });
    }
};
