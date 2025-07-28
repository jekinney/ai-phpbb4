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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_pm_banned')->default(false)->after('ban_reason');
            $table->timestamp('pm_banned_at')->nullable()->after('is_pm_banned');
            $table->text('pm_ban_reason')->nullable()->after('pm_banned_at');
            $table->foreignId('pm_banned_by')->nullable()->constrained('users')->nullOnDelete()->after('pm_ban_reason');
            $table->timestamp('pm_ban_expires_at')->nullable()->after('pm_banned_by');
            
            // Index for performance
            $table->index(['is_pm_banned', 'pm_ban_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pm_banned_by']);
            $table->dropIndex(['is_pm_banned', 'pm_ban_expires_at']);
            $table->dropColumn([
                'is_pm_banned',
                'pm_banned_at',
                'pm_ban_reason',
                'pm_banned_by',
                'pm_ban_expires_at'
            ]);
        });
    }
};
