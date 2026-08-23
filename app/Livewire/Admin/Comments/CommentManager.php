<?php

namespace App\Livewire\Admin\Comments;

use App\Models\Comment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Comments')]
class CommentManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updating($name): void
    {
        if (in_array($name, ['search', 'status'])) {
            $this->resetPage();
        }
    }

    public function approve(int $id): void
    {
        Comment::whereKey($id)->update(['status' => 'approved']);
        session()->flash('status', __('Comment approved.'));
    }

    public function markSpam(int $id): void
    {
        Comment::whereKey($id)->update(['status' => 'spam']);
        session()->flash('status', __('Comment marked as spam.'));
    }

    public function delete(int $id): void
    {
        Comment::whereKey($id)->delete();
        session()->flash('status', __('Comment deleted.'));
    }

    public function render()
    {
        $comments = Comment::with('post', 'user')
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->where('body', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.comments.index', [
            'comments' => $comments,
            'pendingCount' => Comment::pending()->count(),
            'approvedCount' => Comment::approved()->count(),
        ]);
    }
}
