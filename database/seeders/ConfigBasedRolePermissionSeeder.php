<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConfigBasedRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder intelligently creates new records and updates existing ones
     * only when changes are detected. It does NOT truncate any tables.
     */
    public function run(): void
    {
        $this->command->info('🔐 Starting intelligent ACL system sync from config...');
        $this->command->info('   (No data will be truncated - only creating/updating as needed)');

        DB::transaction(function () {
            $this->createPermissions();
            $this->createRoles();
            $this->assignPermissionsToRoles();
            $this->createSuperAdminUser();
        });

        $this->command->info('✅ ACL system synchronized successfully!');
    }

    /**
     * Create permissions from config
     */
    private function createPermissions(): void
    {
        $this->command->info('📝 Processing permissions...');

        $permissionsConfig = config('acl.permissions');
        $existingPermissions = DB::table('permissions')->pluck('display_name', 'name')->toArray();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($permissionsConfig as $category => $categoryPermissions) {
            foreach ($categoryPermissions as $name => $displayName) {
                if (!isset($existingPermissions[$name])) {
                    // Create new permission
                    DB::table('permissions')->insert([
                        'name' => $name,
                        'display_name' => $displayName,
                        'category' => $category,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                } else {
                    // Check if update is needed
                    $existingPermission = DB::table('permissions')->where('name', $name)->first();
                    $updates = [];
                    
                    if ($existingPermission->display_name !== $displayName) {
                        $updates['display_name'] = $displayName;
                    }
                    
                    if ($existingPermission->category !== $category) {
                        $updates['category'] = $category;
                    }
                    
                    if (!empty($updates)) {
                        $updates['updated_at'] = now();
                        DB::table('permissions')->where('name', $name)->update($updates);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            }
        }

        $this->command->info("   ✓ Permissions: {$created} created, {$updated} updated, {$skipped} unchanged");
    }

    /**
     * Create roles from config
     */
    private function createRoles(): void
    {
        $this->command->info('👥 Processing roles...');

        $rolesConfig = config('acl.roles');
        $existingRoles = DB::table('roles')->pluck('id', 'name')->toArray();
        
        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rolesConfig as $name => $roleData) {
            if (!isset($existingRoles[$name])) {
                // Create new role
                DB::table('roles')->insert([
                    'name' => $name,
                    'display_name' => $roleData['display_name'],
                    'level' => $roleData['level'],
                    'is_default' => $roleData['is_default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            } else {
                // Check if update is needed
                $existingRole = DB::table('roles')->where('name', $name)->first();
                $updates = [];
                
                if ($existingRole->display_name !== $roleData['display_name']) {
                    $updates['display_name'] = $roleData['display_name'];
                }
                
                if ($existingRole->level !== $roleData['level']) {
                    $updates['level'] = $roleData['level'];
                }
                
                if ($existingRole->is_default !== $roleData['is_default']) {
                    $updates['is_default'] = $roleData['is_default'];
                }
                
                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('roles')->where('name', $name)->update($updates);
                    $updated++;
                } else {
                    $skipped++;
                }
            }
        }

        $this->command->info("   ✓ Roles: {$created} created, {$updated} updated, {$skipped} unchanged");
    }

    /**
     * Assign permissions to roles based on config
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('🔗 Processing role permissions...');

        $rolesConfig = config('acl.roles');
        $allPermissions = DB::table('permissions')->pluck('id', 'name')->toArray();
        $roles = DB::table('roles')->pluck('id', 'name')->toArray();
        
        $totalAssigned = 0;
        $totalRemoved = 0;
        $totalSkipped = 0;

        foreach ($rolesConfig as $roleName => $roleData) {
            if (!isset($roles[$roleName])) {
                continue; // Role doesn't exist, skip
            }
            
            $roleId = $roles[$roleName];
            $configPermissions = $roleData['permissions'];
            
            // Get permissions this role should have
            $shouldHavePermissions = [];
            if ($configPermissions === 'all') {
                $shouldHavePermissions = array_keys($allPermissions);
            } elseif (is_array($configPermissions)) {
                $shouldHavePermissions = array_intersect($configPermissions, array_keys($allPermissions));
            }
            
            // Get permissions this role currently has
            $currentPermissions = DB::table('role_permissions')
                ->where('role_id', $roleId)
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.name')
                ->toArray();
            
            // Determine what to add and remove
            $toAdd = array_diff($shouldHavePermissions, $currentPermissions);
            $toRemove = array_diff($currentPermissions, $shouldHavePermissions);
            
            // Remove permissions that shouldn't be there
            if (!empty($toRemove)) {
                $removeIds = array_intersect_key($allPermissions, array_flip($toRemove));
                DB::table('role_permissions')
                    ->where('role_id', $roleId)
                    ->whereIn('permission_id', $removeIds)
                    ->delete();
                $totalRemoved += count($toRemove);
            }
            
            // Add new permissions
            if (!empty($toAdd)) {
                $rolePermissions = [];
                foreach ($toAdd as $permissionName) {
                    if (isset($allPermissions[$permissionName])) {
                        $rolePermissions[] = [
                            'role_id' => $roleId,
                            'permission_id' => $allPermissions[$permissionName],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                
                if (!empty($rolePermissions)) {
                    DB::table('role_permissions')->insert($rolePermissions);
                    $totalAssigned += count($rolePermissions);
                }
            }
            
            // Count unchanged permissions
            $unchanged = array_intersect($shouldHavePermissions, $currentPermissions);
            $totalSkipped += count($unchanged);
            
            $this->command->info("   ✓ {$roleName}: " . count($toAdd) . " added, " . count($toRemove) . " removed, " . count($unchanged) . " unchanged");
        }

        $this->command->info("   ✓ Total: {$totalAssigned} permissions assigned, {$totalRemoved} removed, {$totalSkipped} unchanged");
    }

    /**
     * Create super admin user from config
     */
    private function createSuperAdminUser(): void
    {
        $this->command->info('👑 Processing super admin user...');

        $superAdminConfig = config('acl.super_admin_user');
        
        // Check if user already exists
        $existingUser = DB::table('users')->where('email', $superAdminConfig['email'])->first();
        
        if ($existingUser) {
            $this->command->info("   ✓ Found existing user: {$superAdminConfig['email']}");
            
            // Check if user details need updating
            $updates = [];
            if ($existingUser->name !== $superAdminConfig['name']) {
                $updates['name'] = $superAdminConfig['name'];
            }
            
            // Only update password if it's different (check hash)
            if (!Hash::check($superAdminConfig['password'], $existingUser->password)) {
                $updates['password'] = Hash::make($superAdminConfig['password']);
            }
            
            // Ensure email is verified
            if ($existingUser->email_verified_at === null) {
                $updates['email_verified_at'] = now();
            }
            
            if (!empty($updates)) {
                $updates['updated_at'] = now();
                DB::table('users')->where('id', $existingUser->id)->update($updates);
                $this->command->info("   ✓ Updated user details");
            } else {
                $this->command->info("   ✓ User details unchanged");
            }
            
            $userId = $existingUser->id;
        } else {
            $userId = DB::table('users')->insertGetId([
                'name' => $superAdminConfig['name'],
                'email' => $superAdminConfig['email'],
                'password' => Hash::make($superAdminConfig['password']),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("   ✓ Created new user: {$superAdminConfig['email']}");
        }

        // Assign super admin role if not already assigned
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        if (!$superAdminRole) {
            $this->command->error("   ✗ Super admin role not found!");
            return;
        }
        
        $existingRole = DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $superAdminRole->id)
            ->exists();

        if (!$existingRole) {
            DB::table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $superAdminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("   ✓ Assigned super_admin role");
        } else {
            $this->command->info("   ✓ User already has super_admin role");
        }
    }
}
