<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Carbon;
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
            'views' => (int) Post::where('type', 'post')->sum('views'),
            'categories' => Category::count(),
        ];

        $publishedByDay = Post::where('type', 'post')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', now()->subDays(13)->startOfDay())
            ->toBase()
            ->pluck('published_at')
            ->countBy(fn ($publishedAt) => Carbon::parse($publishedAt)->toDateString());

        $publishingActivity = collect(range(13, 0))->map(function (int $daysAgo) use ($publishedByDay) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->format('j M'),
                'count' => $publishedByDay->get($date->toDateString(), 0),
            ];
        });

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recentPosts' => Post::with('author')->latest()->take(6)->get(),
            'recentComments' => Comment::with(['post', 'user'])->latest()->take(6)->get(),
            'publishingActivity' => $publishingActivity,
            'maxActivity' => max(1, (int) $publishingActivity->max('count')),
            'categoryStats' => Category::withCount(['posts' => fn ($query) => $query->where('status', 'published')])
                ->orderByDesc('posts_count')->take(6)->get(),
        ]);
    }
}
