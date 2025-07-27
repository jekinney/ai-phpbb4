<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AclSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:sync {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync ACL permissions and roles from configuration (non-destructive)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN: Showing what would be changed...');
            $this->newLine();
            
            // For now, just inform that dry-run isn't implemented yet
            $this->warn('Dry-run mode not yet implemented. Use --validate with acl:info to check config.');
            return 0;
        }

        $this->info('🔄 Syncing ACL system from configuration...');
        $this->info('   (This is safe - no existing data will be lost)');
        $this->newLine();

        try {
            // Run the non-destructive seeder
            Artisan::call('db:seed', [
                '--class' => 'ConfigBasedRolePermissionSeeder',
                '--force' => true,
            ]);

            $this->info('✅ ACL system synchronized successfully!');
            $this->newLine();

            // Show the updated configuration
            $this->info('📊 Current ACL Status:');
            Artisan::call('acl:info');

        } catch (\Exception $e) {
            $this->error('❌ Failed to sync ACL system: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
