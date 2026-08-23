<?php

namespace App\Livewire\Admin\Tags;

use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Tags')]
class TagManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public ?int $editingId = null;
    public bool $showModal = false;

    public array $name = [];
    public string $slug = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $tag->id;
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = $tag->getTranslation('name', $loc, false);
        }
        $this->slug = $tag->slug;
        $this->showModal = true;
    }

    public function save(): void
    {
        $default = config('barta.default_locale', 'bn');
        $this->validate([
            "name.$default" => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $tag = $this->editingId ? Tag::findOrFail($this->editingId) : new Tag();
        $tag->setTranslations('name', array_filter($this->name, 'filled'));
        if (filled($this->slug)) {
            $tag->slug = Str::slug($this->slug) ?: $this->slug;
        }
        $tag->save();

        session()->flash('status', __('Tag saved.'));
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Tag::whereKey($id)->delete();
        session()->flash('status', __('Tag deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'slug']);
        $this->name = [];
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = '';
        }
    }

    public function render()
    {
        $tags = Tag::withCount('posts')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    foreach (barta_locales() as $loc) {
                        $sub->orWhere('name->'.$loc, 'like', '%'.$this->search.'%');
                    }
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('livewire.admin.tags.index', ['tags' => $tags]);
    }
}
