<?php

namespace App\Livewire\Admin\Themes;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Themes')]
class ThemeManagerPage extends Component
{
    public function activate(string $slug): void
    {
        app('barta.theme')->activate($slug);
        session()->flash('status', __('Theme activated.'));
    }

    public function render()
    {
        $manager = app('barta.theme');

        return view('livewire.admin.themes.index', [
            'themes' => $manager->all(),
            'active' => $manager->active(),
        ]);
    }
}
