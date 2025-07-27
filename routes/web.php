<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Models\Forum;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', App\Livewire\Login::class)->name('login');
    Route::get('/register', App\Livewire\Register::class)->name('register');
});

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
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Admin routes - protected by admin permission
Route::middleware(['auth', 'permission:access_admin_panel'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', App\Livewire\Admin\Dashboard::class)->name('dashboard');
    
    Route::get('/users', App\Livewire\Admin\UserIndex::class)->name('users.index');
    
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    
    Route::get('/roles', App\Livewire\Admin\RoleIndex::class)->name('roles.index');
    
    Route::middleware(['permission:manage_home_page'])->group(function () {
        Route::get('/home-page', App\Livewire\Admin\HomePageManager::class)->name('home-page.index');
    });
    
    Route::get('/permissions', function () {
        return view('admin.permissions.index');
    })->name('permissions.index');
    
    Route::get('/forums', function () {
        return view('admin.forums.index');
    })->name('forums.index');
    
    Route::get('/forums/{forum}', function (App\Models\Forum $forum) {
        return view('admin.forums.show', ['forum' => $forum]);
    })->name('forums.show');
    
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('settings.index');
    
    Route::get('/logs', function () {
        return view('admin.logs.index');
    })->name('logs.index');
});

require __DIR__.'/auth.php';
