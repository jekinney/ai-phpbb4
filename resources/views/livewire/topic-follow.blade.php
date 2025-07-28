<div class="flex items-center space-x-3">
    <!-- Follow/Unfollow Button -->
    @auth
        <div class="flex items-center space-x-2">
            <button 
                wire:click="toggleFollow"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white transition-colors duration-200 
                    {{ $isFollowing 
                        ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' 
                        : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500' 
                    }} focus:outline-none focus:ring-2 focus:ring-offset-2"
                title="{{ $isFollowing ? 'Unfollow this topic' : 'Follow this topic to get notifications' }}"
            >
                @if($isFollowing)
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Unfollow
                @else
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Follow
                @endif
            </button>

            <!-- Notification Settings (only show when following) -->
            @if($isFollowing)
                <div class="relative" x-data="{ open: false }">
                    <button 
                        @click="open = !open"
                        class="inline-flex items-center px-2 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        title="Notification settings"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg border border-gray-200 z-50"
                    >
                        <div class="p-3 space-y-3">
                            <h4 class="text-sm font-medium text-gray-900">Notification Settings</h4>
                            
                            <label class="flex items-center space-x-2">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="notifyReplies"
                                    wire:change="updateNotificationSettings"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="text-sm text-gray-700">Notify me of new replies</span>
                            </label>
                            
                            <p class="text-xs text-gray-500">
                                You'll receive notifications when new replies are posted to this topic.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500">Sign in</a> to follow this topic
        </div>
    @endauth

    <!-- Followers Count -->
    <div class="flex items-center text-sm text-gray-500" title="{{ $followersCount }} {{ Str::plural('follower', $followersCount) }}">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span>{{ $followersCount }}</span>
    </div>
</div>
