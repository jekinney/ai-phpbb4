<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaticPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create pages without requiring existing users for now
        // We'll set created_by and updated_by to 1 assuming there will be at least one user
        
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'meta_description' => 'Learn more about our community and mission.',
                'content' => '<div class="prose max-w-none">
                    <h2>Welcome to Our Community</h2>
                    <p>We are a vibrant online community dedicated to bringing people together through meaningful discussions and shared interests.</p>
                    
                    <h3>Our Mission</h3>
                    <p>To provide a safe, welcoming, and engaging platform where members can connect, share knowledge, and build lasting relationships.</p>
                    
                    <h3>Our Values</h3>
                    <ul>
                        <li><strong>Respect:</strong> We treat all members with dignity and courtesy</li>
                        <li><strong>Inclusivity:</strong> Everyone is welcome regardless of background</li>
                        <li><strong>Quality:</strong> We strive for meaningful, high-quality discussions</li>
                        <li><strong>Growth:</strong> We encourage learning and personal development</li>
                    </ul>
                    
                    <h3>Join Us</h3>
                    <p>Whether you\'re here to learn, share, or simply connect with like-minded individuals, we\'re glad you\'re here. Join our community today and be part of something special!</p>
                </div>',
                'is_active' => true,
                'show_in_footer' => true,
                'sort_order' => 10,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'meta_description' => 'Get in touch with our team for support or inquiries.',
                'content' => '<div class="prose max-w-none">
                    <h2>Get In Touch</h2>
                    <p>We\'d love to hear from you! Whether you have questions, suggestions, or need support, we\'re here to help.</p>
                    
                    <h3>Contact Information</h3>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Email</h4>
                                <p class="text-gray-600">support@example.com</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Response Time</h4>
                                <p class="text-gray-600">We typically respond within 24 hours</p>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Frequently Asked Questions</h3>
                    <p>Before reaching out, you might find the answer to your question in our <a href="/pages/faq" class="text-blue-600 hover:text-blue-800">FAQ section</a>.</p>
                    
                    <h3>Community Guidelines</h3>
                    <p>For questions about our community rules and guidelines, please visit our <a href="/pages/community-guidelines" class="text-blue-600 hover:text-blue-800">Community Guidelines</a> page.</p>
                </div>',
                'is_active' => true,
                'show_in_footer' => true,
                'sort_order' => 20,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_description' => 'Our privacy policy explains how we collect, use, and protect your personal information.',
                'content' => '<div class="prose max-w-none">
                    <h2>Privacy Policy</h2>
                    <p><em>Last updated: ' . date('F j, Y') . '</em></p>
                    
                    <h3>Information We Collect</h3>
                    <p>We collect information you provide directly to us, such as when you create an account, participate in forums, or contact us.</p>
                    
                    <h4>Account Information</h4>
                    <ul>
                        <li>Username and email address</li>
                        <li>Profile information you choose to provide</li>
                        <li>Posts and comments you make on the platform</li>
                    </ul>
                    
                    <h4>Automatically Collected Information</h4>
                    <ul>
                        <li>IP address and browser information</li>
                        <li>Pages visited and time spent on the site</li>
                        <li>Device and connection information</li>
                    </ul>
                    
                    <h3>How We Use Your Information</h3>
                    <p>We use the information we collect to:</p>
                    <ul>
                        <li>Provide and maintain our services</li>
                        <li>Communicate with you about your account</li>
                        <li>Improve our platform and user experience</li>
                        <li>Ensure the security of our community</li>
                    </ul>
                    
                    <h3>Information Sharing</h3>
                    <p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>
                    
                    <h3>Data Security</h3>
                    <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
                    
                    <h3>Contact Us</h3>
                    <p>If you have any questions about this Privacy Policy, please <a href="/pages/contact" class="text-blue-600 hover:text-blue-800">contact us</a>.</p>
                </div>',
                'is_active' => true,
                'show_in_footer' => true,
                'sort_order' => 30,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'meta_description' => 'Terms and conditions for using our community platform.',
                'content' => '<div class="prose max-w-none">
                    <h2>Terms of Service</h2>
                    <p><em>Last updated: ' . date('F j, Y') . '</em></p>
                    
                    <h3>Acceptance of Terms</h3>
                    <p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h3>User Accounts</h3>
                    <p>To access certain features of the service, you must create an account. You are responsible for:</p>
                    <ul>
                        <li>Maintaining the confidentiality of your account credentials</li>
                        <li>All activities that occur under your account</li>
                        <li>Providing accurate and up-to-date information</li>
                    </ul>
                    
                    <h3>Community Guidelines</h3>
                    <p>Users must follow our community guidelines, which include:</p>
                    <ul>
                        <li>Being respectful to other community members</li>
                        <li>Not posting spam, offensive, or inappropriate content</li>
                        <li>Respecting intellectual property rights</li>
                        <li>Not engaging in harassment or discriminatory behavior</li>
                    </ul>
                    
                    <h3>Content</h3>
                    <p>Users retain ownership of content they post, but grant us a license to use, display, and distribute such content on our platform.</p>
                    
                    <h3>Prohibited Uses</h3>
                    <p>You may not use our service:</p>
                    <ul>
                        <li>For any unlawful purpose or to solicit unlawful activity</li>
                        <li>To violate any international, federal, provincial, or state regulations or laws</li>
                        <li>To transmit, or procure the sending of, any advertising or promotional material</li>
                        <li>To impersonate or attempt to impersonate another person</li>
                    </ul>
                    
                    <h3>Termination</h3>
                    <p>We may terminate or suspend your account and access to the service immediately, without prior notice, for conduct that we believe violates these Terms of Service.</p>
                    
                    <h3>Contact Information</h3>
                    <p>Questions about the Terms of Service should be sent to us via our <a href="/pages/contact" class="text-blue-600 hover:text-blue-800">contact page</a>.</p>
                </div>',
                'is_active' => true,
                'show_in_footer' => true,
                'sort_order' => 40,
            ],
            [
                'title' => 'Frequently Asked Questions',
                'slug' => 'faq',
                'meta_description' => 'Answers to commonly asked questions about our platform.',
                'content' => '<div class="prose max-w-none">
                    <h2>Frequently Asked Questions</h2>
                    
                    <div class="space-y-8">
                        <div>
                            <h3>Getting Started</h3>
                            
                            <h4>How do I create an account?</h4>
                            <p>Click the "Register" button in the top navigation, fill out the required information, and verify your email address.</p>
                            
                            <h4>Is registration free?</h4>
                            <p>Yes, creating an account and participating in our community is completely free.</p>
                            
                            <h4>Can I change my username later?</h4>
                            <p>Currently, usernames cannot be changed after registration. Please choose carefully when creating your account.</p>
                        </div>
                        
                        <div>
                            <h3>Using the Forums</h3>
                            
                            <h4>How do I create a new topic?</h4>
                            <p>Navigate to the appropriate forum category and click the "New Topic" button. Make sure to choose a descriptive title and provide clear details in your post.</p>
                            
                            <h4>Can I edit my posts?</h4>
                            <p>Yes, you can edit your posts for a limited time after posting. Look for the "Edit" button on your posts.</p>
                            
                            <h4>How do I search for topics?</h4>
                            <p>Use the search bar at the top of the page to find topics, posts, or users.</p>
                        </div>
                        
                        <div>
                            <h3>Account & Privacy</h3>
                            
                            <h4>How do I change my password?</h4>
                            <p>Go to your account settings and look for the "Change Password" option.</p>
                            
                            <h4>Can I delete my account?</h4>
                            <p>Yes, you can request account deletion by contacting our support team. Please note that some content may remain for moderation purposes.</p>
                            
                            <h4>Is my personal information safe?</h4>
                            <p>We take privacy seriously. Please review our <a href="/pages/privacy-policy" class="text-blue-600 hover:text-blue-800">Privacy Policy</a> for detailed information.</p>
                        </div>
                        
                        <div>
                            <h3>Community Guidelines</h3>
                            
                            <h4>What content is not allowed?</h4>
                            <p>We prohibit spam, harassment, hate speech, illegal content, and anything that violates our community guidelines.</p>
                            
                            <h4>How do I report inappropriate content?</h4>
                            <p>Look for the "Report" button on posts or topics, or contact our moderation team directly.</p>
                        </div>
                        
                        <div>
                            <h3>Still Have Questions?</h3>
                            <p>If you can\'t find the answer you\'re looking for, please don\'t hesitate to <a href="/pages/contact" class="text-blue-600 hover:text-blue-800">contact us</a>. Our support team is here to help!</p>
                        </div>
                    </div>
                </div>',
                'is_active' => true,
                'show_in_footer' => true,
                'sort_order' => 50,
            ],
            [
                'title' => 'Community Guidelines',
                'slug' => 'community-guidelines',
                'meta_description' => 'Guidelines for participating in our community forums.',
                'content' => '<div class="prose max-w-none">
                    <h2>Community Guidelines</h2>
                    <p>Our community thrives when everyone feels welcome and respected. These guidelines help maintain a positive environment for all members.</p>
                    
                    <h3>Core Principles</h3>
                    
                    <h4>1. Be Respectful</h4>
                    <ul>
                        <li>Treat all members with courtesy and respect</li>
                        <li>Disagree with ideas, not people</li>
                        <li>Avoid personal attacks, insults, or inflammatory language</li>
                        <li>Respect different viewpoints and experiences</li>
                    </ul>
                    
                    <h4>2. Stay On Topic</h4>
                    <ul>
                        <li>Keep discussions relevant to the forum topic</li>
                        <li>Use descriptive subject lines</li>
                        <li>Search before posting to avoid duplicates</li>
                        <li>Post in the appropriate forum category</li>
                    </ul>
                    
                    <h4>3. Be Helpful</h4>
                    <ul>
                        <li>Share knowledge and experience generously</li>
                        <li>Provide constructive feedback</li>
                        <li>Help newcomers feel welcome</li>
                        <li>Thank others for helpful contributions</li>
                    </ul>
                    
                    <h3>Prohibited Content</h3>
                    <p>The following types of content are not allowed:</p>
                    
                    <h4>Spam and Self-Promotion</h4>
                    <ul>
                        <li>Excessive self-promotion or advertising</li>
                        <li>Repetitive or irrelevant posts</li>
                        <li>Link dropping without context</li>
                    </ul>
                    
                    <h4>Inappropriate Content</h4>
                    <ul>
                        <li>Hate speech or discriminatory language</li>
                        <li>Harassment or threats</li>
                        <li>Adult or explicit content</li>
                        <li>Illegal activities or content</li>
                    </ul>
                    
                    <h4>Misinformation</h4>
                    <ul>
                        <li>Deliberately false or misleading information</li>
                        <li>Medical advice from non-professionals</li>
                        <li>Conspiracy theories or unsubstantiated claims</li>
                    </ul>
                    
                    <h3>Consequences</h3>
                    <p>Violations of these guidelines may result in:</p>
                    <ul>
                        <li>Warning messages</li>
                        <li>Temporary suspension</li>
                        <li>Permanent ban for serious or repeated violations</li>
                    </ul>
                    
                    <h3>Reporting</h3>
                    <p>If you see content that violates these guidelines:</p>
                    <ul>
                        <li>Use the "Report" button on the post</li>
                        <li>Contact our moderation team</li>
                        <li>Do not engage with trolls or disruptive users</li>
                    </ul>
                    
                    <h3>Appeals</h3>
                    <p>If you believe a moderation action was taken in error, you can appeal by <a href="/pages/contact" class="text-blue-600 hover:text-blue-800">contacting us</a> with details about your situation.</p>
                    
                    <p class="text-sm text-gray-600 mt-8">These guidelines may be updated periodically. By participating in our community, you agree to follow the current version of these guidelines.</p>
                </div>',
                'is_active' => true,
                'show_in_footer' => false,
                'sort_order' => 60,
            ]
        ];

        foreach ($pages as $pageData) {
            StaticPage::create(array_merge($pageData, [
                'created_by' => 1, // Default to user ID 1
                'updated_by' => 1, // Default to user ID 1
            ]));
        }

        $this->command->info('Created ' . count($pages) . ' static pages successfully.');
    }
}
