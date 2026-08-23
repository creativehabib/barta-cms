<?php

namespace App\Livewire\Admin\Posts;

use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Posts')]
class PostIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $type = 'post';

    #[Url]
    public string $category = '';

    public function updating($name): void
    {
        if (in_array($name, ['search', 'status', 'type', 'category'])) {
            $this->resetPage();
        }
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->authorizeOwnership($post);
        $post->delete();
        session()->flash('status', __('Post moved to trash.'));
    }

    public function toggleFeatured(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->authorizeOwnership($post);
        $post->update(['is_featured' => ! $post->is_featured]);
    }

    protected function authorizeOwnership(Post $post): void
    {
        $user = auth()->user();
        // Authors may only touch their own posts; editors+ may touch any.
        if (! $user->hasAnyRole(['super-admin', 'admin', 'editor']) && $post->user_id !== $user->id) {
            abort(403);
        }
    }

    public function render()
    {
        $posts = Post::with('author', 'category')
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    foreach (barta_locales() as $loc) {
                        $sub->orWhere('title->'.$loc, 'like', '%'.$this->search.'%');
                    }
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.posts.index', [
            'posts' => $posts,
            'categories' => Category::orderBy('id')->get(),
        ]);
    }
}
