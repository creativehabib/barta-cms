<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Plan;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Plans')]
class PlanManager extends Component
{
    public ?int $editingId = null;
    public bool $showModal = false;

    public array $name = [];
    public array $description = [];
    public string $slug = '';
    public $price = 0;
    public string $currency = 'BDT';
    public string $interval = 'month';
    public int $interval_count = 1;
    public string $featuresText = '';
    public bool $is_active = true;
    public int $position = 0;

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $plan = Plan::findOrFail($id);
        $this->editingId = $plan->id;
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = $plan->getTranslation('name', $loc, false);
            $this->description[$loc] = $plan->getTranslation('description', $loc, false);
        }
        $this->slug = $plan->slug;
        $this->price = $plan->price;
        $this->currency = $plan->currency;
        $this->interval = $plan->interval;
        $this->interval_count = (int) $plan->interval_count;
        $this->featuresText = implode("\n", $plan->features ?? []);
        $this->is_active = (bool) $plan->is_active;
        $this->position = (int) $plan->position;
        $this->showModal = true;
    }

    public function save(): void
    {
        $default = config('barta.default_locale', 'bn');
        $this->validate([
            "name.$default" => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'interval' => ['required', 'in:day,week,month,year,lifetime'],
            'interval_count' => ['required', 'integer', 'min:1'],
        ]);

        $features = collect(explode("\n", $this->featuresText))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();

        $plan = $this->editingId ? Plan::findOrFail($this->editingId) : new Plan();
        $plan->setTranslations('name', array_filter($this->name, 'filled'));
        $plan->setTranslations('description', array_filter($this->description, 'filled'));
        $plan->price = $this->price;
        $plan->currency = $this->currency;
        $plan->interval = $this->interval;
        $plan->interval_count = $this->interval_count;
        $plan->features = $features;
        $plan->is_active = $this->is_active;
        $plan->position = $this->position;

        $slugSource = filled($this->slug)
            ? $this->slug
            : ($this->name[$default] ?? ($this->name['en'] ?? 'plan'));
        $plan->slug = Str::slug($slugSource) ?: 'plan-'.Str::lower(Str::random(6));

        $plan->save();

        session()->flash('status', __('Plan saved.'));
        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        Plan::whereKey($id)->delete();
        session()->flash('status', __('Plan deleted.'));
    }

    protected function resetForm(): void
    {
        $this->reset(['editingId', 'slug', 'price', 'currency', 'interval', 'interval_count', 'featuresText', 'is_active', 'position']);
        $this->currency = 'BDT';
        $this->interval = 'month';
        $this->interval_count = 1;
        $this->is_active = true;
        $this->price = 0;
        $this->name = [];
        $this->description = [];
        foreach (barta_locales() as $loc) {
            $this->name[$loc] = '';
            $this->description[$loc] = '';
        }
    }

    public function render()
    {
        return view('livewire.admin.plans.index', [
            'plans' => Plan::withCount('subscriptions')->orderBy('position')->orderBy('id')->get(),
        ]);
    }
}
