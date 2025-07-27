<?php

namespace App\Livewire;

use App\Models\HomePageContent;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Livewire\Component;

class HomePage extends Component
{
    public $searchQuery = '';
    public $showSearchResults = false;

    public function updatedSearchQuery()
    {
        $this->showSearchResults = !empty($this->searchQuery);
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->showSearchResults = false;
    }

    public function getSearchResultsProperty()
    {
        if (empty($this->searchQuery)) {
            return [];
        }

        // Placeholder search results
        $placeholderResults = [
            [
                'type' => 'forum',
                'title' => 'Laravel Discussion',
                'description' => 'Discuss Laravel framework topics'
            ],
            [
                'type' => 'category',
                'title' => 'General Discussion',
                'description' => 'General topics and discussions'
            ],
            [
                'type' => 'forum',
                'title' => 'Technical Support',
                'description' => 'Get help with technical issues'
            ],
            [
                'type' => 'category',
                'title' => 'Feature Requests',
                'description' => 'Suggest new features'
            ],
            [
                'type' => 'forum',
                'title' => 'Bug Reports',
                'description' => 'Report bugs and issues'
            ],
        ];

        return collect($placeholderResults)
            ->filter(function ($item) {
                return stripos($item['title'], $this->searchQuery) !== false ||
                       stripos($item['description'], $this->searchQuery) !== false;
            })
            ->take(5)
            ->values()
            ->toArray();
    }

    public function getHeroTitleProperty()
    {
        return HomePageContent::getByKey('hero', 'title', 'Welcome to AI-phpBB4');
    }

    public function getHeroSubtitleProperty()
    {
        return HomePageContent::getByKey('hero', 'subtitle', 'A next-generation community platform powered by AI and built with modern Laravel technology. Connect, discuss, and grow with our vibrant community.');
    }

    public function getCtaTitleProperty()
    {
        return HomePageContent::getByKey('cta', 'title', 'Ready to join our community?');
    }

    public function getCtaSubtitleProperty()
    {
        return HomePageContent::getByKey('cta', 'subtitle', 'Connect with like-minded individuals, share your knowledge, and be part of the next generation forum platform.');
    }

    public function getStatsProperty()
    {
        return [
            'total_users' => User::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'online_users' => 1, // Placeholder - would implement real online tracking
        ];
    }

    public function getNewsArticlesProperty()
    {
        return HomePageContent::bySection('news')
            ->active()
            ->ordered()
            ->get();
    }

    public function render()
    {
        return view('pure-livewire-home', [
            'heroTitle' => $this->heroTitle,
            'heroSubtitle' => $this->heroSubtitle,
            'ctaTitle' => $this->ctaTitle,
            'ctaSubtitle' => $this->ctaSubtitle,
            'stats' => $this->stats,
            'newsArticles' => $this->newsArticles,
            'showSearchResults' => $this->showSearchResults,
            'searchResults' => $this->searchResults,
        ]);
    }
}
