<?php

namespace App\Livewire\Admin\Subscribers;

use App\Models\Subscriber;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Subscribers')]
class SubscriberManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public bool $showModal = false;

    public string $email = '';
    public string $name = '';
    public string $locale = 'bn';

    public function updating($name): void
    {
        if (in_array($name, ['search', 'status'])) {
            $this->resetPage();
        }
    }

    public function create(): void
    {
        $this->reset(['email', 'name']);
        $this->locale = config('barta.default_locale', 'bn');
        $this->resetErrorBag();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255', 'unique:subscribers,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'locale' => ['required', 'string', 'max:10'],
        ]);

        Subscriber::create([
            'email' => $this->email,
            'name' => $this->name ?: null,
            'locale' => $this->locale,
            'status' => 'subscribed',
            'token' => Str::random(40),
            'verified_at' => now(),
        ]);

        session()->flash('status', __('Subscriber added.'));
        $this->showModal = false;
        $this->reset(['email', 'name']);
    }

    public function delete(int $id): void
    {
        Subscriber::whereKey($id)->delete();
        session()->flash('status', __('Subscriber removed.'));
    }

    public function render()
    {
        $subscribers = Subscriber::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('email', 'like', '%'.$this->search.'%')
                        ->orWhere('name', 'like', '%'.$this->search.'%');
                });
            })
            ->latest()
            ->paginate(25);

        return view('livewire.admin.subscribers.index', [
            'subscribers' => $subscribers,
            'total' => Subscriber::count(),
            'subscribedCount' => Subscriber::subscribed()->count(),
            'pendingCount' => Subscriber::where('status', 'pending')->count(),
        ]);
    }
}
