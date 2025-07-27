<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class AclResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:reset {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset ACL permissions and roles from configuration (destructive - dev only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('⚠️  WARNING: This command will DESTROY all existing ACL data!');
        $this->warn('⚠️  For normal updates, use: php artisan db:seed --class=ConfigBasedRolePermissionSeeder');
        $this->newLine();
        
        if (!$this->option('force') && !$this->confirm('This will reset all ACL data. Continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('🔄 Resetting ACL system...');

        try {
            DB::beginTransaction();

            // Clear existing ACL data but preserve users
            $this->info('  • Clearing existing ACL data...');
            DB::table('user_permissions')->delete();
            DB::table('user_roles')->delete();
            DB::table('role_permissions')->delete();
            DB::table('roles')->delete();
            DB::table('permissions')->delete();

            DB::commit();

            // Re-seed the ACL data
            $this->info('  • Re-seeding ACL data...');
            Artisan::call('db:seed', [
                '--class' => 'ConfigBasedRolePermissionSeeder',
                '--force' => true,
            ]);

            $this->info('✅ ACL system reset successfully!');

            // Display summary
            $this->newLine();
            Artisan::call('acl:info');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Failed to reset ACL system: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
