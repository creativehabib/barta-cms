<?php

namespace App\Livewire\Admin\Plugins;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Plugins')]
class PluginManagerPage extends Component
{
    public function scan(): void
    {
        app('barta.plugin')->sync();
        session()->flash('status', __('Plugins re-scanned.'));
    }

    public function activate(string $slug): void
    {
        app('barta.plugin')->activate($slug);
        session()->flash('status', __('Plugin activated. Reload to apply its hooks.'));
    }

    public function deactivate(string $slug): void
    {
        app('barta.plugin')->deactivate($slug);
        session()->flash('status', __('Plugin deactivated.'));
    }

    public function render()
    {
        $manager = app('barta.plugin');
        $manager->sync();

        return view('livewire.admin.plugins.index', [
            'plugins' => $manager->all(),
            'activeSlugs' => $manager->activeSlugs(),
        ]);
    }
}
