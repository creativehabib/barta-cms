<?php

namespace App\Livewire\Admin\Widgets;

use App\Models\Widget;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Widgets')]
class WidgetManager extends Component
{
    public const AREAS = [
        'sidebar' => 'Sidebar',
        'home-top' => 'Home top',
        'home-bottom' => 'Home bottom',
        'footer-1' => 'Footer 1',
        'footer-2' => 'Footer 2',
        'footer-3' => 'Footer 3',
    ];

    public const TYPES = [
        'recent_posts' => 'Recent posts',
        'popular_posts' => 'Popular posts',
        'category_list' => 'Category list',
        'tag_cloud' => 'Tag cloud',
        'newsletter' => 'Newsletter box',
        'html' => 'Custom HTML',
        'ad' => 'Ad slot',
    ];

    public ?int $editingId = null;
    public bool $showModal = false;

    public array $title = [];
    public string $area = 'sidebar';
    public string $type = 'recent_posts';
    public int $position = 0;
    public bool $is_active = true;
    public string $settingsJson = '';

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $widget = Widget::findOrFail($id);
        $this->editingId = $widget->id;
        foreach (barta_locales() as $loc) {
            $this->title[$loc] = $widget->getTranslation('title', $loc, false);
        }
        $this->area = $widget->area;
        $this->type = $widget->type;
        $this->position = (int) $widget->position;
        $this->is_active = (bool) $widget->is_active;
        $this->settingsJson = $widget->settings ? json_encode($widget->settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'area' => ['required', 'string'],
            'type' => ['required', 'string'],
            'settingsJson' => ['nullable', 'string'],
        ]);

        $settings = [];
        if (filled($this->settingsJson)) {
            $decoded = json_decode($this->settingsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->addError('settingsJson', __('Invalid JSON.'));

                return;
            }
            $settings = $decoded ?: [];
        }

        $widget = $this->editingId ? Widget::findOrFail($this->editingId) : new Widget();
        $widget->setTranslations('title', array_filter($this->title, 'filled'));
        $widget->area = $this->area;
        $widget->type = $this->type;
        $widget->position = $this->position;
        $widget->is_active = $this->is_active;
        $widget->settings = $settings;
        $widget->save();

        session()->flash('status', __('Widget saved.'));
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Widget::whereKey($id)->delete();
        session()->flash('status', __('Widget deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'area', 'type', 'position', 'is_active', 'settingsJson']);
        $this->area = 'sidebar';
        $this->type = 'recent_posts';
        $this->is_active = true;
        $this->title = [];
        foreach (barta_locales() as $loc) {
            $this->title[$loc] = '';
        }
    }

    public function render()
    {
        return view('livewire.admin.widgets.index', [
            'areas' => self::AREAS,
            'types' => self::TYPES,
            'widgetsByArea' => Widget::orderBy('position')->get()->groupBy('area'),
        ]);
    }
}
