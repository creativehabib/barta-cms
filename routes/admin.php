<?php

use App\Livewire\Admin\Ads\AdManager;
use App\Livewire\Admin\Categories\CategoryManager;
use App\Livewire\Admin\Comments\CommentManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Media\MediaLibrary;
use App\Livewire\Admin\Menus\MenuManager;
use App\Livewire\Admin\Newsletters\NewsletterManager;
use App\Livewire\Admin\Plans\PlanManager;
use App\Livewire\Admin\Plugins\PluginManagerPage;
use App\Livewire\Admin\Posts\PostForm;
use App\Livewire\Admin\Posts\PostIndex;
use App\Livewire\Admin\Settings\SettingsPage;
use App\Livewire\Admin\Subscribers\SubscriberManager;
use App\Livewire\Admin\Tags\TagManager;
use App\Livewire\Admin\Themes\ThemeManagerPage;
use App\Livewire\Admin\Users\UserManager;
use App\Livewire\Admin\Widgets\WidgetManager;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');

        // Content
        Route::get('/posts', PostIndex::class)->name('posts.index');
        Route::get('/posts/create', PostForm::class)->name('posts.create');
        Route::get('/posts/{post}/edit', PostForm::class)->name('posts.edit');

        Route::get('/categories', CategoryManager::class)->name('categories.index');
        Route::get('/tags', TagManager::class)->name('tags.index');
        Route::get('/comments', CommentManager::class)->name('comments.index');
        Route::get('/media', MediaLibrary::class)->name('media.index');

        // Appearance
        Route::get('/menus', MenuManager::class)->name('menus.index');
        Route::get('/widgets', WidgetManager::class)->name('widgets.index');
        Route::get('/themes', ThemeManagerPage::class)->name('themes.index');
        Route::get('/plugins', PluginManagerPage::class)->name('plugins.index');

        // Monetisation
        Route::get('/ads', AdManager::class)->name('ads.index');
        Route::get('/plans', PlanManager::class)->name('plans.index');

        // Audience
        Route::get('/subscribers', SubscriberManager::class)->name('subscribers.index');
        Route::get('/newsletters', NewsletterManager::class)->name('newsletters.index');

        // People & configuration
        Route::get('/users', UserManager::class)
            ->middleware('permission:manage users')->name('users.index');
        Route::get('/settings', SettingsPage::class)
            ->middleware('permission:manage settings')->name('settings');
    });
