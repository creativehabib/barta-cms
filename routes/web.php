<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ThemeAssetController;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Front\Plans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / front-end
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Language switch (?to=bn|en)
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// Newsletter subscribe + comments
Route::post('/newsletter/subscribe', [NewsletterController::class, 'store'])->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::post('/comments/{post}', [CommentController::class, 'store'])->name('comments.store');

// Serve theme assets that live outside the public root.
Route::get('/themes/{theme}/assets/{path}', ThemeAssetController::class)
    ->where('path', '.*')->name('theme.asset');

/*
|--------------------------------------------------------------------------
| Premium / subscriptions & payments
|--------------------------------------------------------------------------
*/
Route::get('/plans', Plans::class)->name('plans');

// Starting a purchase requires a signed-in reader.
Route::post('/checkout/{plan:slug}', [PaymentController::class, 'checkout'])->middleware('auth')->name('checkout');

// Gateway callbacks are driven by the gateway, not the browser session, so they
// stay OUT of the auth group: a cross-site POST return may not carry the
// SameSite=Lax session cookie. Both are CSRF-exempt (see bootstrap/app.php) and
// re-resolve the user/plan from the {payment} reference.
Route::match(['get', 'post'], '/payment/{gateway}/return/{payment}', [PaymentController::class, 'return'])->name('payment.return');
Route::post('/payment/{gateway}/ipn/{payment}', [PaymentController::class, 'ipn'])->name('payment.ipn');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel (staff only) — see routes/admin.php
|--------------------------------------------------------------------------
*/
require __DIR__.'/admin.php';

/*
|--------------------------------------------------------------------------
| Reader account area
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'index'])->name('account');
});

/*
|--------------------------------------------------------------------------
| Front-end content routes — MUST be last so the permalink catch-all does
| not shadow the named routes above.
|--------------------------------------------------------------------------
*/
Route::get('/author/{user:username}', [AuthorController::class, 'show'])->name('author');
Route::get('/tag/{tag:slug}', [TagController::class, 'show'])->name('tag');
Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category');
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('page');

// Permalink catch-all → resolves a post by the final {slug} segment.
Route::get('/{path}', [PostController::class, 'show'])
    ->where('path', '.*')
    ->name('post');
