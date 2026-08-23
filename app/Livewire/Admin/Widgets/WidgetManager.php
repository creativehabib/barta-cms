<?php

namespace App\Livewire\Admin\Widgets;

use App\Models\Widget;
use Illuminate\Support\Facades\DB;
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
    public string $orderMessage = '';

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
        $isNew = ! $widget->exists;
        $widget->setTranslations('title', array_filter($this->title, 'filled'));
        $widget->area = $this->area;
        $widget->type = $this->type;
        $widget->position = $isNew
            ? ((int) Widget::where('area', $this->area)->max('position')) + 1
            : $this->position;
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

    /** Persist the order produced by the drag-and-drop widget board. */
    public function reorderWidgets(array $areas): void
    {
        $allowedAreas = array_keys(self::AREAS);

        if (collect($areas)->only($allowedAreas)->contains(fn ($ids) => ! is_array($ids))) {
            $this->addError('order', __('The widget order could not be saved. Please refresh and try again.'));

            return;
        }

        $submittedIds = collect($areas)
            ->only($allowedAreas)
            ->flatten()
            ->map(fn ($id) => (int) $id);

        if ($submittedIds->duplicates()->isNotEmpty()) {
            $this->addError('order', __('A widget cannot appear in more than one area.'));

            return;
        }

        $validIds = Widget::whereKey($submittedIds->all())->pluck('id')->map(fn ($id) => (int) $id);
        if ($validIds->count() !== $submittedIds->count()) {
            $this->addError('order', __('The widget order could not be saved. Please refresh and try again.'));

            return;
        }

        DB::transaction(function () use ($areas, $allowedAreas): void {
            foreach ($allowedAreas as $area) {
                foreach (array_values($areas[$area] ?? []) as $position => $id) {
                    Widget::whereKey((int) $id)->update([
                        'area' => $area,
                        'position' => $position + 1,
                    ]);
                }
            }
        });

        $this->resetErrorBag('order');
        $this->orderMessage = __('Widget layout saved.');
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
