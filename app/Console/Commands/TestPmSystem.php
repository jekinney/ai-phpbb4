<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PersonalMessage;
use App\Models\PersonalMessageParticipant;

class TestPmSystem extends Command
{
    protected $signature = 'test:pm-system';
    protected $description = 'Test the Personal Messaging system';

    public function handle()
    {
        $this->info('Testing Personal Messaging System...');

        // Get users
        $sender = User::first(); // Get the first user (main admin user)
        $recipient = User::where('email', 'john@example.com')->first();

        if (!$sender) {
            $this->error('No users found in database');
            return;
        }

        if (!$recipient) {
            $this->error('Recipient (john@example.com) not found');
            return;
        }

        // Create test message
        $message = PersonalMessage::create([
            'subject' => 'Welcome to the PM System!',
            'content' => 'This is a test message to verify the Personal Messaging system is working correctly. You can reply to this message to test the reply functionality.',
            'sender_id' => $sender->id,
            'is_draft' => false
        ]);

        // Add recipient
        PersonalMessageParticipant::create([
            'message_id' => $message->id,
            'user_id' => $recipient->id,
            'type' => 'to'
        ]);

        $this->info("✅ Test message created successfully!");
        $this->info("📧 Message ID: {$message->id}");
        $this->info("👤 From: {$sender->name} ({$sender->email})");
        $this->info("👤 To: {$recipient->name} ({$recipient->email})");
        $this->info("📝 Subject: {$message->subject}");

        // Test unread count
        $unreadCount = $recipient->unread_messages_count;
        $this->info("📬 Unread count for {$recipient->name}: {$unreadCount}");

        // Test message queries
        $userMessages = PersonalMessage::forUser($recipient)->count();
        $this->info("💬 Total messages for {$recipient->name}: {$userMessages}");

        $this->info('✅ PM System test completed successfully!');
    }
}
