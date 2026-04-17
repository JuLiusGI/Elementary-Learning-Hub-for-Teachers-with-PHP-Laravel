<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_sync_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('client_id', 36)->unique()->comment('UUID from client');
            $table->enum('action', ['create', 'update', 'delete']);
            $table->string('model_type', 100);
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('payload');
            $table->timestamp('client_timestamp');
            $table->timestamp('synced_at')->nullable();
            $table->enum('sync_status', ['pending', 'synced', 'conflict', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('sync_status', 'idx_sync_status');
            $table->index('user_id', 'idx_sync_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_sync_queue');
    }
};
