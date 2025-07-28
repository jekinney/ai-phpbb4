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
        Schema::create('topic_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->boolean('notify_replies')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
            
            // Ensure unique follow relationship
            $table->unique(['user_id', 'topic_id']);
            
            // Index for performance
            $table->index(['topic_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_follows');
    }
};
