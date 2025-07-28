<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topic_follow_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('reply_author_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'is_read']);
            $table->index(['topic_id', 'created_at']);
            $table->index(['post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_follow_notifications');
    }
};
