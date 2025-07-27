<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Livewire\Component;

class HomePage extends Component
{
    public $searchQuery = '';
    public $searchResults = [];
    public $showSearchResults = false;

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 2) {
            $this->searchResults = [
                // Placeholder search results - will be replaced with actual forum/category search
                [
                    'type' => 'category',
                    'title' => 'General Discussion',
                    'description' => 'General topics and community discussions'
                ],
                [
                    'type' => 'forum',
                    'title' => 'Site Announcements',
                    'description' => 'Important updates and news about the community'
                ],
                [
                    'type' => 'forum',
                    'title' => 'Tech Support',
                    'description' => 'Get help with technical issues'
                ],
            ];
            
            // Filter results based on search query
            $this->searchResults = collect($this->searchResults)
                ->filter(function ($item) {
                    return str_contains(strtolower($item['title']), strtolower($this->searchQuery)) ||
                           str_contains(strtolower($item['description']), strtolower($this->searchQuery));
                })
                ->take(5)
                ->toArray();
                
            $this->showSearchResults = !empty($this->searchResults);
        } else {
            $this->searchResults = [];
            $this->showSearchResults = false;
        }
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->showSearchResults = false;
    }

    public function render()
    {
        $stats = [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'online_users' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ];

        return view('livewire.home-page', compact('stats'))
            ->layout('layouts.app')
            ->title('Welcome to AI-phpBB4 Community');
    }
}
