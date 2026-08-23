<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'posts' => Post::where('type', 'post')->count(),
            'published' => Post::where('type', 'post')->where('status', 'published')->count(),
            'drafts' => Post::where('type', 'post')->where('status', 'draft')->count(),
            'pending_comments' => Comment::pending()->count(),
            'users' => User::count(),
            'subscribers' => Subscriber::subscribed()->count(),
            'active_subscriptions' => Subscription::active()->count(),
            'revenue' => (float) Payment::where('status', 'success')->sum('amount'),
        ];

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recentPosts' => Post::with('author')->latest()->take(6)->get(),
            'recentComments' => Comment::with('post')->latest()->take(6)->get(),
        ]);
    }
}
