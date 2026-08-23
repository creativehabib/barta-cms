<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Settings')]
class SettingsPage extends Component
{
    public string $tab = 'general';

    public array $form = [];

    /** Setting keys managed by this page, with their defaults. */
    protected function defaults(): array
    {
        return [
            'site_name' => config('app.name', 'Barta'),
            'site_tagline' => '',
            'site_description' => '',
            'posts_per_page' => 12,
            'permalink_structure' => config('barta.permalink', 'date'),
            'permalink_custom' => '/%category%/%postname%/',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_youtube' => '',
            'social_instagram' => '',
            'meta_description' => '',
            'google_analytics_id' => '',
            'contact_email' => '',
            'contact_phone' => '',
        ];
    }

    public function mount(): void
    {
        $repo = app('barta.settings');
        foreach ($this->defaults() as $key => $default) {
            $this->form[$key] = $repo->get($key, $default);
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.site_name' => ['required', 'string', 'max:255'],
            'form.posts_per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'form.permalink_structure' => ['required', 'in:default,date,day,postname,category,custom'],
            'form.permalink_custom' => ['required_if:form.permalink_structure,custom', 'string', 'max:255', 'regex:/^\/[A-Za-z0-9%_\-\/]+\/$/'],
            'form.contact_email' => ['nullable', 'email'],
        ]);

        // Cast posts_per_page to int so it stores as an int type.
        $this->form['posts_per_page'] = (int) $this->form['posts_per_page'];

        app('barta.settings')->setMany($this->form);

        session()->flash('status', __('Settings saved.'));
    }

    public function render()
    {
        return view('livewire.admin.settings.index', [
            'permalinks' => config('barta.permalinks', []),
        ]);
    }
}
