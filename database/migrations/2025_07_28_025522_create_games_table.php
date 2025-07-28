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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('scoring_type')->default('highest'); // highest, lowest, time
            $table->integer('max_players_per_game')->default(1);
            $table->json('settings')->nullable(); // Game-specific settings
            $table->enum('reset_frequency', ['never', 'daily', 'weekly', 'monthly'])->default('never');
            $table->timestamp('last_reset_at')->nullable();
            $table->timestamp('next_reset_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
