<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;

class RoleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showModal = false;
    public $editingRole = null;
    public $roleName = '';
    public $roleDisplayName = '';
    public $roleDescription = '';
    public $roleLevel = 1;
    public $selectedPermissions = [];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    protected $rules = [
        'roleName' => 'required|string|max:255|unique:roles,name',
        'roleDisplayName' => 'required|string|max:255',
        'roleDescription' => 'nullable|string|max:500',
        'roleLevel' => 'required|integer|min:1|max:10',
        'selectedPermissions' => 'array',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['roleName', 'roleDisplayName', 'roleDescription', 'roleLevel', 'selectedPermissions', 'editingRole']);
        $this->roleLevel = 1;
        $this->showModal = true;
    }

    public function openEditModal($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        $this->editingRole = $role;
        $this->roleName = $role->name;
        $this->roleDisplayName = $role->display_name;
        $this->roleDescription = $role->description;
        $this->roleLevel = $role->level;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['roleName', 'roleDisplayName', 'roleDescription', 'roleLevel', 'selectedPermissions', 'editingRole']);
    }

    public function saveRole()
    {
        if ($this->editingRole) {
            $this->rules['roleName'] = 'required|string|max:255|unique:roles,name,' . $this->editingRole->id;
        }

        $this->validate();

        if ($this->editingRole) {
            // Update existing role
            $this->editingRole->update([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName,
                'description' => $this->roleDescription,
                'level' => $this->roleLevel,
            ]);
            $role = $this->editingRole;
        } else {
            // Create new role
            $role = Role::create([
                'name' => $this->roleName,
                'display_name' => $this->roleDisplayName,
                'description' => $this->roleDescription,
                'level' => $this->roleLevel,
                'is_default' => false,
            ]);
        }

        // Sync permissions
        $role->permissions()->sync($this->selectedPermissions);

        $this->dispatch('role-saved', $this->editingRole ? 'updated' : 'created');
        $this->closeModal();
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        
        // Prevent deletion of system roles
        if (in_array($role->name, ['super_admin', 'administrator', 'user'])) {
            $this->dispatch('role-delete-error', 'Cannot delete system role');
            return;
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            $this->dispatch('role-delete-error', 'Cannot delete role with assigned users');
            return;
        }

        $role->delete();
        $this->dispatch('role-deleted', $role->display_name);
    }

    public function render()
    {
        $roles = Role::query()
            ->withCount('users')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('display_name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->paginate($this->perPage);

        $permissions = Permission::orderBy('category')->orderBy('name')->get()->groupBy('category');

        return view('livewire.admin.role-index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ])
        ->layout('layouts.admin')
        ->title('Role Management');
    }
}
