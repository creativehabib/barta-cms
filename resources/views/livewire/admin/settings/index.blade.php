<div class="mx-auto max-w-3xl space-y-5">
    <h1 class="text-2xl font-black">{{ __('Settings') }}</h1>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 rounded-xl border border-ink-100 bg-white p-1 text-sm">
        @foreach (['general' => __('General'), 'reading' => __('Reading'), 'social' => __('Social'), 'seo' => __('SEO'), 'contact' => __('Contact')] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'rounded-lg px-4 py-2 font-semibold',
                        'bg-brand-600 text-white' => $tab === $key,
                        'text-ink-600 hover:bg-ink-50' => $tab !== $key,
                    ])>{{ $label }}</button>
        @endforeach
    </div>

    <form wire:submit="save" class="space-y-5">
        <div class="rounded-xl border border-ink-100 bg-white p-5">
            {{-- General --}}
            @if ($tab === 'general')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Site name') }}</label>
                        <input type="text" wire:model="form.site_name" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('form.site_name') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Tagline') }}</label>
                        <input type="text" wire:model="form.site_tagline" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Description') }}</label>
                        <textarea wire:model="form.site_description" rows="3" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500"></textarea>
                    </div>
                </div>
            @endif

            {{-- Reading --}}
            @if ($tab === 'reading')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Posts per page') }}</label>
                        <input type="number" min="1" max="100" wire:model="form.posts_per_page" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('form.posts_per_page') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Permalink structure') }}</label>
                        <select wire:model="form.permalink_structure" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @foreach ($permalinks as $key => $pattern)
                                <option value="{{ $key }}">{{ ucfirst($key) }} — /{{ $pattern }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-ink-400">{{ __('Controls how article URLs are built.') }}</p>
                    </div>
                </div>
            @endif

            {{-- Social --}}
            @if ($tab === 'social')
                <div class="space-y-4">
                    @foreach (['social_facebook' => 'Facebook', 'social_twitter' => 'X / Twitter', 'social_youtube' => 'YouTube', 'social_instagram' => 'Instagram'] as $key => $label)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ $label }}</label>
                            <input type="url" wire:model="form.{{ $key }}" placeholder="https://" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- SEO --}}
            @if ($tab === 'seo')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Default meta description') }}</label>
                        <textarea wire:model="form.meta_description" rows="3" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Google Analytics ID') }}</label>
                        <input type="text" wire:model="form.google_analytics_id" placeholder="G-XXXXXXX" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            @endif

            {{-- Contact --}}
            @if ($tab === 'contact')
                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Contact email') }}</label>
                        <input type="email" wire:model="form.contact_email" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('form.contact_email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Contact phone') }}</label>
                        <input type="text" wire:model="form.contact_phone" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>
                </div>
            @endif
        </div>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-brand-600 px-6 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save settings') }}</button>
        </div>
    </form>
</div>
