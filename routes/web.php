<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\FileUploadController;
use App\Models\Forum;
use App\Models\Topic;
use App\Models\PersonalMessage;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('forums.index');
})->name('home');

// Forum routes - using Livewire components
Route::get('/forums', function () {
    return view('livewire-wrapper.forums-index');
})->name('forums.index');

Route::get('/forums/{forum}', function (Forum $forum) {
    return view('livewire-wrapper.forum-show', ['forum' => $forum]);
})->name('forums.show');

// Topic routes - using Livewire components
Route::get('/topics/{topic}', function (Topic $topic) {
    // Increment view count
    $topic->increment('views_count');
    return view('livewire-wrapper.topic-show', ['topic' => $topic]);
})->name('topics.show');

Route::middleware(['auth'])->group(function () {
    // Topic creation - using Livewire component
    Route::get('/forums/{forum}/topics/create', function (Forum $forum) {
        return view('livewire-wrapper.topic-create', ['forum' => $forum]);
    })->name('topics.create');
    
    // Traditional routes for edit functionality (can be converted to Livewire later)
    Route::get('/topics/{topic}/edit', function (Topic $topic) {
        // For now, redirect to topic show - edit functionality can be added to Livewire later
        return redirect()->route('topics.show', $topic);
    })->name('topics.edit');
    
    Route::put('/topics/{topic}', function (Topic $topic) {
        // Handle topic update if needed
        return redirect()->route('topics.show', $topic);
    })->name('topics.update');
    
    Route::delete('/topics/{topic}', function (Topic $topic) {
        $forum = $topic->forum;
        $topic->delete();
        return redirect()->route('forums.show', $forum);
    })->name('topics.destroy');
    
    // Post management - keeping traditional routes for now
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // File upload routes
    Route::post('/files/upload', [FileUploadController::class, 'upload'])->name('files.upload');
    Route::delete('/files/{attachment}', [FileUploadController::class, 'destroy'])->name('files.destroy');
    
    // Personal Messages routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', function () {
            return view('livewire-wrapper.messages.inbox');
        })->name('inbox');
        
        Route::get('/sent', function () {
            return view('livewire-wrapper.messages.inbox', ['activeTab' => 'sent']);
        })->name('sent');
        
        Route::get('/unread', function () {
            return view('livewire-wrapper.messages.inbox', ['activeTab' => 'unread']);
        })->name('unread');
        
        Route::get('/compose', function () {
            return view('livewire-wrapper.messages.compose');
        })->name('compose');
        
        Route::get('/compose/{recipient}', function ($recipient) {
            return view('livewire-wrapper.messages.compose', ['recipient' => $recipient]);
        })->name('compose.to');
        
        Route::get('/reply/{replyTo}', function ($replyTo) {
            return view('livewire-wrapper.messages.compose', ['replyTo' => $replyTo]);
        })->name('reply');
        
        Route::get('/{message}', function (PersonalMessage $message) {
            return view('livewire-wrapper.messages.show', ['message' => $message]);
        })->name('show');
    });
});

// Public file download route
Route::get('/files/{attachment}/download', [FileUploadController::class, 'download'])->name('files.download');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::get('/documentation', function () {
        return view('livewire-wrapper.admin.documentation-viewer');
    })->name('documentation');
    
    Route::get('/files', function () {
        return view('livewire-wrapper.admin.file-manager');
    })->name('files');
    
    Route::get('/static-pages', function () {
        return view('livewire-wrapper.admin.static-pages');
    })->name('static-pages');
    
    Route::get('/pm-bans', function () {
        return view('livewire-wrapper.admin.pm-ban-management');
    })->name('pm-bans');
});

require __DIR__.'/auth.php';
