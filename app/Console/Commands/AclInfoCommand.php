<?php

namespace App\Console\Commands;

use App\Services\AclConfigService;
use Illuminate\Console\Command;

class AclInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:info {--validate : Validate the ACL configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display ACL system configuration information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('validate')) {
            return $this->validateConfig();
        }

        $this->info('🛡️  ACL System Configuration');
        $this->newLine();

        $this->displayRoles();
        $this->displayPermissions();
        $this->displaySuperAdminConfig();
    }

    private function validateConfig()
    {
        $this->info('🔍 Validating ACL Configuration...');
        
        $errors = AclConfigService::validateConfig();
        
        if (empty($errors)) {
            $this->info('✅ ACL configuration is valid!');
            return 0;
        }

        $this->error('❌ ACL configuration has errors:');
        foreach ($errors as $error) {
            $this->error("  • {$error}");
        }
        
        return 1;
    }

    private function displayRoles()
    {
        $this->info('👥 Roles:');
        
        $roles = AclConfigService::getRoleHierarchy();
        
        $headers = ['Role', 'Display Name', 'Level', 'Default', 'Permissions'];
        $rows = [];

        foreach ($roles as $role) {
            $permissions = AclConfigService::getRolePermissions($role['name']);
            $permissionCount = $permissions->count();
            
            $rows[] = [
                $role['name'],
                $role['display_name'],
                $role['level'],
                $role['is_default'] ? 'Yes' : 'No',
                $permissionCount . ($permissions->count() > 30 ? ' (All)' : ''),
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    private function displayPermissions()
    {
        $this->info('🔐 Permissions by Category:');
        
        $categories = AclConfigService::getPermissionCategories();
        
        foreach ($categories as $category) {
            $permissions = AclConfigService::getPermissionsByCategory($category);
            $this->info("  📁 {$category} ({$permissions->count()} permissions)");
            
            foreach ($permissions as $permission) {
                $this->line("    • {$permission['name']} - {$permission['display_name']}");
            }
            $this->newLine();
        }
    }

    private function displaySuperAdminConfig()
    {
        $this->info('👑 Super Admin Configuration:');
        
        $config = AclConfigService::getSuperAdminConfig();
        
        $this->line("  Name: {$config['name']}");
        $this->line("  Email: {$config['email']}");
        $this->line("  Password: " . str_repeat('*', strlen($config['password'])));
        $this->newLine();
    }
}
