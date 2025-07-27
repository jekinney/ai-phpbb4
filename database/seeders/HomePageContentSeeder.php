<?php

namespace Database\Seeders;

use App\Models\HomePageContent;
use Illuminate\Database\Seeder;

class HomePageContentSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $content = [
            // Hero Section
            [
                'section' => 'hero',
                'key' => 'title',
                'title' => 'Hero Title',
                'content' => 'Welcome to AI-phpBB4',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'section' => 'hero',
                'key' => 'subtitle',
                'title' => 'Hero Subtitle',
                'content' => 'A next-generation community platform powered by AI and built with modern Laravel technology. Connect, discuss, and grow with our vibrant community.',
                'is_active' => true,
                'sort_order' => 2,
            ],

            // News Articles
            [
                'section' => 'news',
                'key' => 'news_1',
                'title' => 'Advanced ACL System Launched',
                'content' => 'We\'ve implemented a comprehensive role-based access control system with 34 granular permissions across 6 categories. This enables fine-tuned control over user capabilities and forum management.',
                'metadata' => [
                    'date' => '2025-01-27',
                    'category' => 'Platform Update',
                    'author' => 'System Administrator',
                    'author_title' => 'Platform Team',
                    'author_avatar' => 'https://ui-avatars.com/api/?name=System+Admin&color=7F9CF5&background=EBF4FF'
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'section' => 'news',
                'key' => 'news_2',
                'title' => 'Livewire Admin Dashboard',
                'content' => 'Experience the new interactive admin dashboard built with Livewire. Real-time user management, role assignments, and system monitoring without page refreshes.',
                'metadata' => [
                    'date' => '2025-01-25',
                    'category' => 'Feature Release',
                    'author' => 'Development Team',
                    'author_title' => 'Engineering',
                    'author_avatar' => 'https://ui-avatars.com/api/?name=Dev+Team&color=10B981&background=D1FAE5'
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'section' => 'news',
                'key' => 'news_3',
                'title' => 'Welcome to AI-phpBB4 Beta',
                'content' => 'Join our growing community of developers, enthusiasts, and contributors. Built on Laravel 12 with modern technologies like Livewire, Alpine.js, and Tailwind CSS.',
                'metadata' => [
                    'date' => '2025-01-20',
                    'category' => 'Community',
                    'author' => 'Community Manager',
                    'author_title' => 'Community Team',
                    'author_avatar' => 'https://ui-avatars.com/api/?name=Community+Manager&color=8B5CF6&background=EDE9FE'
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Call to Action
            [
                'section' => 'cta',
                'key' => 'title',
                'title' => 'CTA Title',
                'content' => 'Ready to join our community?',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'section' => 'cta',
                'key' => 'subtitle',
                'title' => 'CTA Subtitle',
                'content' => 'Connect with like-minded individuals, share your knowledge, and be part of the next generation forum platform.',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($content as $item) {
            HomePageContent::updateOrCreate(
                ['section' => $item['section'], 'key' => $item['key']],
                $item
            );
        }
    }
}
