<?php

namespace App\Livewire\Admin\Menus;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Menus')]
class MenuManager extends Component
{
    public ?int $selectedMenuId = null;

    // Menu form
    public bool $showMenuModal = false;
    public string $menuName = '';
    public string $menuLocation = '';

    // Item form
    public bool $showItemModal = false;
    public ?int $editingItemId = null;
    public array $itemLabel = [];
    public string $itemType = 'custom';
    public string $itemUrl = '';
    public ?int $itemTargetId = null;
    public string $itemTarget = '_self';
    public ?int $itemParentId = null;
    public int $itemPosition = 0;

    public function mount(): void
    {
        $this->selectedMenuId = Menu::query()->value('id');
    }

    public function selectMenu(int $id): void
    {
        $this->selectedMenuId = $id;
    }

    // ---- Menus ----
    public function createMenu(): void
    {
        $this->reset(['menuName', 'menuLocation']);
        $this->resetErrorBag();
        $this->showMenuModal = true;
    }

    public function saveMenu(): void
    {
        $this->validate([
            'menuName' => ['required', 'string', 'max:255'],
            'menuLocation' => ['required', 'string', 'max:255'],
        ]);

        $menu = Menu::create([
            'name' => $this->menuName,
            'location' => $this->menuLocation,
        ]);

        $this->selectedMenuId = $menu->id;
        session()->flash('status', __('Menu created.'));
        $this->showMenuModal = false;
    }

    public function deleteMenu(int $id): void
    {
        Menu::whereKey($id)->delete();
        MenuItem::where('menu_id', $id)->delete();
        if ($this->selectedMenuId === $id) {
            $this->selectedMenuId = Menu::query()->value('id');
        }
        session()->flash('status', __('Menu deleted.'));
    }

    // ---- Items ----
    public function createItem(): void
    {
        $this->reset(['editingItemId', 'itemUrl', 'itemTargetId', 'itemParentId']);
        $this->itemType = 'custom';
        $this->itemTarget = '_self';
        $this->itemLabel = [];
        foreach (barta_locales() as $loc) {
            $this->itemLabel[$loc] = '';
        }
        $this->itemPosition = (int) MenuItem::where('menu_id', $this->selectedMenuId)->max('position') + 1;
        $this->resetErrorBag();
        $this->showItemModal = true;
    }

    public function editItem(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $this->editingItemId = $item->id;
        foreach (barta_locales() as $loc) {
            $this->itemLabel[$loc] = $item->getTranslation('label', $loc, false);
        }
        $this->itemType = $item->type;
        $this->itemUrl = (string) $item->url;
        $this->itemTargetId = $item->target_id;
        $this->itemTarget = $item->target ?: '_self';
        $this->itemParentId = $item->parent_id;
        $this->itemPosition = (int) $item->position;
        $this->showItemModal = true;
    }

    public function saveItem(): void
    {
        $default = config('barta.default_locale', 'bn');
        $this->validate([
            "itemLabel.$default" => ['required', 'string', 'max:255'],
            'itemType' => ['required', 'in:custom,category,page,post'],
        ]);

        $item = $this->editingItemId ? MenuItem::findOrFail($this->editingItemId) : new MenuItem();
        $item->menu_id = $this->selectedMenuId;
        $item->setTranslations('label', array_filter($this->itemLabel, 'filled'));
        $item->type = $this->itemType;
        $item->url = $this->itemType === 'custom' ? ($this->itemUrl ?: null) : null;
        $item->target_id = in_array($this->itemType, ['category', 'page', 'post']) ? $this->itemTargetId : null;
        $item->target = $this->itemTarget;
        $item->parent_id = $this->itemParentId ?: null;
        $item->position = $this->itemPosition;
        $item->save();

        session()->flash('status', __('Menu item saved.'));
        $this->showItemModal = false;
    }

    public function deleteItem(int $id): void
    {
        MenuItem::where('parent_id', $id)->update(['parent_id' => null]);
        MenuItem::whereKey($id)->delete();
        session()->flash('status', __('Menu item deleted.'));
    }

    public function render()
    {
        $menu = $this->selectedMenuId
            ? Menu::with(['allItems'])->find($this->selectedMenuId)
            : null;

        return view('livewire.admin.menus.index', [
            'menus' => Menu::orderBy('name')->get(),
            'menu' => $menu,
            'items' => $menu ? $menu->allItems : collect(),
            'categories' => Category::orderBy('id')->get(),
            'pages' => Post::pages()->orderBy('id')->get(),
        ]);
    }
}
