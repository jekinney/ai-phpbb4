<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('forums.index');
})->name('home');

// Forum routes
Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');
Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');

// Topic routes
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');

Route::middleware(['auth'])->group(function () {
    // Topic management
    Route::get('/forums/{forum}/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/forums/{forum}/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
    Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
    
    // Post management
    Route::post('/topics/{topic}/posts', [PostController::class, 'store'])->name('posts.store');
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

require __DIR__.'/auth.php';
