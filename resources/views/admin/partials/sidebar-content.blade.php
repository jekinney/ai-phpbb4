<div class="flex flex-1 flex-col overflow-y-auto pt-5 pb-4">
    <div class="flex flex-shrink-0 items-center px-4">
        <h2 class="text-lg font-semibold text-white">Admin Panel</h2>
    </div>
    <nav class="mt-5 flex-1 space-y-1 px-2">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Dashboard
        </a>

        <!-- Users Management -->
        <div class="space-y-1">
            <a href="{{ route('admin.users.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Users
            </a>
        </div>

        <!-- ACL Management -->
        <div class="space-y-1">
            <button type="button" class="group flex w-full items-center rounded-md px-2 py-2 text-left text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white" 
                    x-data="{ open: {{ request()->routeIs('admin.roles.*', 'admin.permissions.*') ? 'true' : 'false' }} }" 
                    @click="open = !open" 
                    :aria-expanded="open">
                <svg class="mr-3 h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
                Access Control
                <svg class="ml-auto h-5 w-5 transform transition-colors duration-150 ease-in-out group-hover:text-gray-400" 
                     :class="{'rotate-90 text-gray-400': open, 'text-gray-300': !open}" 
                     viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <div class="space-y-1" x-show="open" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition-all ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                <a href="{{ route('admin.roles.index') }}" class="group flex items-center rounded-md py-2 pl-11 pr-2 text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Roles
                </a>
                <a href="{{ route('admin.permissions.index') }}" class="group flex items-center rounded-md py-2 pl-11 pr-2 text-sm font-medium {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    Permissions
                </a>
            </div>
        </div>

        <!-- Forums Management -->
        <a href="{{ route('admin.forums.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.forums.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.forums.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
            </svg>
            Forums
        </a>

        <!-- Home Page Management -->
        @can('manage_home_page')
        <a href="{{ route('admin.home-page.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.home-page.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.home-page.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Home Page
        </a>
        @endcan

        <!-- Settings -->
        <a href="{{ route('admin.settings.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.settings.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>

        <!-- System Logs -->
        <a href="{{ route('admin.logs.index') }}" class="group flex items-center rounded-md px-2 py-2 text-sm font-medium {{ request()->routeIs('admin.logs.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="mr-3 h-6 w-6 flex-shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Logs
        </a>
    </nav>
</div>

<div class="flex flex-shrink-0 bg-gray-700 p-4">
    <div class="group block w-full flex-shrink-0">
        <div class="flex items-center">
            <div class="ml-3">
                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs font-medium text-gray-300 group-hover:text-gray-200">Administrator</p>
            </div>
        </div>
    </div>
</div>
