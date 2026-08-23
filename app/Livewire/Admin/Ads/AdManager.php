<?php

namespace App\Livewire\Admin\Ads;

use App\Models\Ad;
use App\Models\AdSlot;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Advertisements')]
class AdManager extends Component
{
    // Slot form
    public bool $showSlotModal = false;
    public ?int $editingSlotId = null;
    public string $slotKey = '';
    public string $slotName = '';
    public ?int $slotWidth = null;
    public ?int $slotHeight = null;

    // Ad form
    public bool $showAdModal = false;
    public ?int $editingAdId = null;
    public ?int $ad_slot_id = null;
    public string $adName = '';
    public string $adType = 'image';
    public string $imagePath = '';
    public string $content = '';
    public string $linkUrl = '';
    public ?string $startsAt = null;
    public ?string $endsAt = null;
    public bool $adActive = true;

    // ---- Slots ----
    public function createSlot(): void
    {
        $this->reset(['editingSlotId', 'slotKey', 'slotName', 'slotWidth', 'slotHeight']);
        $this->resetErrorBag();
        $this->showSlotModal = true;
    }

    public function editSlot(int $id): void
    {
        $slot = AdSlot::findOrFail($id);
        $this->editingSlotId = $slot->id;
        $this->slotKey = $slot->key;
        $this->slotName = $slot->name;
        $this->slotWidth = $slot->width;
        $this->slotHeight = $slot->height;
        $this->showSlotModal = true;
    }

    public function saveSlot(): void
    {
        $this->validate([
            'slotKey' => ['required', 'string', 'max:255', 'unique:ad_slots,key,'.($this->editingSlotId ?? 'NULL').',id'],
            'slotName' => ['required', 'string', 'max:255'],
            'slotWidth' => ['nullable', 'integer', 'min:0'],
            'slotHeight' => ['nullable', 'integer', 'min:0'],
        ]);

        $slot = $this->editingSlotId ? AdSlot::findOrFail($this->editingSlotId) : new AdSlot();
        $slot->key = $this->slotKey;
        $slot->name = $this->slotName;
        $slot->width = $this->slotWidth;
        $slot->height = $this->slotHeight;
        $slot->save();

        session()->flash('status', __('Ad slot saved.'));
        $this->showSlotModal = false;
    }

    public function deleteSlot(int $id): void
    {
        AdSlot::whereKey($id)->delete();
        session()->flash('status', __('Ad slot deleted.'));
    }

    // ---- Ads ----
    public function createAd(): void
    {
        $this->reset(['editingAdId', 'ad_slot_id', 'adName', 'imagePath', 'content', 'linkUrl', 'startsAt', 'endsAt']);
        $this->adType = 'image';
        $this->adActive = true;
        $this->ad_slot_id = AdSlot::query()->value('id');
        $this->resetErrorBag();
        $this->showAdModal = true;
    }

    public function editAd(int $id): void
    {
        $ad = Ad::findOrFail($id);
        $this->editingAdId = $ad->id;
        $this->ad_slot_id = $ad->ad_slot_id;
        $this->adName = $ad->name;
        $this->adType = $ad->type;
        $this->imagePath = (string) $ad->image_path;
        $this->content = (string) $ad->content;
        $this->linkUrl = (string) $ad->link_url;
        $this->startsAt = $ad->starts_at?->format('Y-m-d');
        $this->endsAt = $ad->ends_at?->format('Y-m-d');
        $this->adActive = (bool) $ad->is_active;
        $this->showAdModal = true;
    }

    public function saveAd(): void
    {
        $this->validate([
            'ad_slot_id' => ['required', 'exists:ad_slots,id'],
            'adName' => ['required', 'string', 'max:255'],
            'adType' => ['required', 'in:image,html,adsense'],
            'imagePath' => ['nullable', 'string', 'max:2048'],
            'linkUrl' => ['nullable', 'url', 'max:2048'],
            'startsAt' => ['nullable', 'date'],
            'endsAt' => ['nullable', 'date'],
        ]);

        $ad = $this->editingAdId ? Ad::findOrFail($this->editingAdId) : new Ad();
        $ad->ad_slot_id = $this->ad_slot_id;
        $ad->name = $this->adName;
        $ad->type = $this->adType;
        $ad->image_path = $this->adType === 'image' ? ($this->imagePath ?: null) : null;
        $ad->content = in_array($this->adType, ['html', 'adsense']) ? ($this->content ?: null) : null;
        $ad->link_url = $this->linkUrl ?: null;
        $ad->starts_at = $this->startsAt ?: null;
        $ad->ends_at = $this->endsAt ?: null;
        $ad->is_active = $this->adActive;
        $ad->save();

        session()->flash('status', __('Advertisement saved.'));
        $this->showAdModal = false;
    }

    public function deleteAd(int $id): void
    {
        Ad::whereKey($id)->delete();
        session()->flash('status', __('Advertisement deleted.'));
    }

    public function render()
    {
        return view('livewire.admin.ads.index', [
            'slots' => AdSlot::withCount('ads')->orderBy('key')->get(),
            'ads' => Ad::with('slot')->latest()->get(),
        ]);
    }
}
