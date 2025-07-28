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
        Schema::create('personal_messages', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('content');
            $table->text('content_html')->nullable(); // For rich text content
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_draft')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['sender_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_messages');
    }
};
