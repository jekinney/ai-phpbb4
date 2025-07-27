<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
        <!-- Page header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Role Management
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Manage roles and their associated permissions.
                </p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <button 
                    wire:click="openCreateModal"
                    type="button" 
                    class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Role
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        id="search"
                        placeholder="Search roles..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>

                <!-- Per Page -->
                <div>
                    <label for="per-page" class="block text-sm font-medium text-gray-700">Show</label>
                    <select 
                        wire:model.live="perPage"
                        id="per-page"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Roles table -->
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        Role
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Description
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Level
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Users
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Type
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($roles as $role)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $role->display_name }}</div>
                                            <div class="text-gray-500">{{ $role->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">
                                        {{ $role->description ?? 'No description' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                            Level {{ $role->level }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($role->is_default)
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                Default
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                Custom
                                            </span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button 
                                                wire:click="openEditModal({{ $role->id }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                Edit
                                            </button>
                                            @if(!in_array($role->name, ['super_admin', 'administrator', 'user']))
                                            <button 
                                                wire:click="deleteRole({{ $role->id }})"
                                                wire:confirm="Are you sure you want to delete this role?"
                                                class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        @if($search)
                                            No roles found matching "{{ $search }}".
                                        @else
                                            No roles found.
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="mt-6">
            {{ $roles->links() }}
        </div>
        @endif
    </div>

    <!-- Role Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }">
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>

            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:p-6 sm:align-middle">
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ $editingRole ? 'Edit Role' : 'Create New Role' }}
                        </h3>
                        <div class="mt-6">
                            <form wire:submit="saveRole">
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <!-- Role Name -->
                                    <div>
                                        <label for="roleName" class="block text-sm font-medium text-gray-700">Role Name</label>
                                        <input wire:model="roleName" type="text" id="roleName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('roleName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Display Name -->
                                    <div>
                                        <label for="roleDisplayName" class="block text-sm font-medium text-gray-700">Display Name</label>
                                        <input wire:model="roleDisplayName" type="text" id="roleDisplayName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('roleDisplayName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="sm:col-span-2">
                                        <label for="roleDescription" class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea wire:model="roleDescription" id="roleDescription" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                        @error('roleDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <!-- Level -->
                                    <div>
                                        <label for="roleLevel" class="block text-sm font-medium text-gray-700">Level</label>
                                        <input wire:model="roleLevel" type="number" id="roleLevel" min="1" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        @error('roleLevel') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <!-- Permissions -->
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700">Permissions</label>
                                    <div class="mt-2 max-h-60 overflow-y-auto border border-gray-300 rounded-md p-4">
                                        @foreach($permissions as $category => $categoryPermissions)
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $category)) }}</h4>
                                            <div class="grid grid-cols-2 gap-2">
                                                @foreach($categoryPermissions as $permission)
                                                <label class="flex items-center">
                                                    <input 
                                                        wire:model="selectedPermissions" 
                                                        type="checkbox" 
                                                        value="{{ $permission->id }}" 
                                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $permission->display_name }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('selectedPermissions') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button type="button" wire:click="closeModal" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Cancel
                                    </button>
                                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        {{ $editingRole ? 'Update Role' : 'Create Role' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@script
<script>
    $wire.on('role-saved', (action) => {
        alert(`Role ${action} successfully!`);
    });

    $wire.on('role-deleted', (roleName) => {
        alert(`Role "${roleName}" has been deleted.`);
    });

    $wire.on('role-delete-error', (message) => {
        alert(`Error: ${message}`);
    });
</script>
@endscript
