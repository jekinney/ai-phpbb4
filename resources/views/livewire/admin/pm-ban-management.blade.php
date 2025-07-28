<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">PM Ban Management</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Manage user bans from the personal messaging system.
        </p>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="p-6">
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <label for="search" class="sr-only">Search users</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input 
                            wire:model.live.debounce.300ms="search"
                            type="text" 
                            id="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Search by username or email..."
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                PM Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ban Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->isPmBanned())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            PM Banned
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Active
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->isPmBanned())
                                        @php $banInfo = $user->getPmBanInfo(); @endphp
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <div><strong>Reason:</strong> {{ Str::limit($banInfo['reason'], 50) }}</div>
                                            @if($banInfo['banned_by'])
                                                <div><strong>Banned by:</strong> {{ $banInfo['banned_by']->name }}</div>
                                            @endif
                                            <div><strong>Date:</strong> {{ $banInfo['banned_at']->format('M j, Y') }}</div>
                                            @if($banInfo['expires_at'])
                                                <div><strong>Expires:</strong> {{ $banInfo['expires_at']->format('M j, Y') }}</div>
                                            @else
                                                <div><strong>Type:</strong> Permanent</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">No ban active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    @if($user->isPmBanned())
                                        <button 
                                            wire:click="openUnbanModal({{ $user->id }})"
                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                        >
                                            Unban
                                        </button>
                                    @else
                                        @unless($user->hasRole('super_admin'))
                                            <button 
                                                wire:click="openBanModal({{ $user->id }})"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                Ban from PM
                                            </button>
                                        @endunless
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Ban Modal -->
    @if($showBanModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeBanModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="banUser">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Ban {{ $selectedUser->name }} from PM System
                                    </h3>
                                    <div class="mt-4 space-y-4">
                                        <!-- Ban Type -->
                                        <div>
                                            <label class="text-base font-medium text-gray-900 dark:text-white">Ban Type</label>
                                            <div class="mt-2 space-y-2">
                                                <label class="flex items-center">
                                                    <input wire:model.live="banType" type="radio" value="permanent" class="form-radio">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Permanent</span>
                                                </label>
                                                <label class="flex items-center">
                                                    <input wire:model.live="banType" type="radio" value="temporary" class="form-radio">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Temporary</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Duration (only for temporary bans) -->
                                        @if($banType === 'temporary')
                                            <div>
                                                <label for="banDuration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Duration (days)
                                                </label>
                                                <input 
                                                    wire:model="banDuration"
                                                    type="number" 
                                                    id="banDuration"
                                                    min="1" 
                                                    max="365"
                                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="Enter number of days"
                                                >
                                                @error('banDuration')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif

                                        <!-- Reason -->
                                        <div>
                                            <label for="banReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Reason for ban
                                            </label>
                                            <textarea 
                                                wire:model="banReason"
                                                id="banReason"
                                                rows="3"
                                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Explain why this user is being banned from the PM system..."
                                            ></textarea>
                                            @error('banReason')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button 
                                type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Ban User
                            </button>
                            <button 
                                type="button"
                                wire:click="closeBanModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Unban Modal -->
    @if($showUnbanModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeUnbanModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Unban {{ $selectedUser->name }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to remove the PM ban from this user? They will be able to send and receive personal messages again.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            wire:click="unbanUser"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Unban User
                        </button>
                        <button 
                            wire:click="closeUnbanModal"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
