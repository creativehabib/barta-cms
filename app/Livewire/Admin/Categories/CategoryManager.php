<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Categories')]
class CategoryManager extends Component
{
    public ?int $editingId = null;
    public bool $showModal = false;

    public array $name = [];
    public array $description = [];
    public ?int $parent_id = null;
    public string $slug = '';
    public string $color = '#c81420';
    public bool $show_in_menu = true;
    public bool $is_active = true;

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = $category->getTranslation('name', $loc, false);
            $this->description[$loc] = $category->getTranslation('description', $loc, false);
        }
        $this->parent_id = $category->parent_id;
        $this->slug = $category->slug;
        $this->color = $category->color ?: '#c81420';
        $this->show_in_menu = $category->show_in_menu;
        $this->is_active = $category->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $default = config('barta.default_locale', 'bn');
        $this->validate(["name.$default" => ['required', 'string', 'max:255']]);

        $category = $this->editingId ? Category::findOrFail($this->editingId) : new Category();
        $category->setTranslations('name', array_filter($this->name, 'filled'));
        $category->setTranslations('description', array_filter($this->description, 'filled'));
        $category->parent_id = $this->parent_id ?: null;
        $category->color = $this->color;
        $category->show_in_menu = $this->show_in_menu;
        $category->is_active = $this->is_active;
        if (filled($this->slug)) {
            $category->slug = Str::slug($this->slug) ?: $this->slug;
        }
        $category->save();

        session()->flash('status', __('Category saved.'));
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Category::whereKey($id)->delete();
        session()->flash('status', __('Category deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'parent_id', 'slug', 'color', 'show_in_menu', 'is_active']);
        $this->color = '#c81420';
        $this->show_in_menu = true;
        $this->is_active = true;
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = '';
            $this->description[$loc] = '';
        }
    }

    public function render()
    {
        return view('livewire.admin.categories.index', [
            'categories' => Category::withCount('posts')->orderBy('position')->orderBy('id')->get(),
        ]);
    }
}
