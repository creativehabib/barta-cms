<div class="mx-auto max-w-3xl space-y-5">
    <h1 class="text-2xl font-black">{{ __('Settings') }}</h1>

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-1 rounded-xl border border-ink-100 bg-white p-1 text-sm">
        @foreach (['general' => __('General'), 'reading' => __('Reading'), 'permalinks' => __('Permalinks'), 'social' => __('Social'), 'seo' => __('SEO'), 'contact' => __('Contact')] as $key => $label)
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
                </div>
            @endif

            {{-- Permalinks --}}
            @if ($tab === 'permalinks')
                <div class="space-y-5">
                    <div>
                        <h2 class="text-lg font-black">{{ __('Permalink structure') }}</h2>
                        <p class="mt-1 text-sm text-ink-500">{{ __('Choose how permanent post URLs should look. Existing posts keep working because requests are resolved by slug.') }}</p>
                    </div>
                    <div class="space-y-2">
                        @foreach (['default' => __('Plain'), 'date' => __('Month and name'), 'day' => __('Day and name'), 'postname' => __('Post name'), 'category' => __('Category and name')] as $key => $label)
                            <label @class([
                                'flex cursor-pointer items-center gap-3 rounded-xl border p-3 transition',
                                'border-brand-500 bg-brand-50 ring-1 ring-brand-200' => $form['permalink_structure'] === $key,
                                'border-ink-100 hover:border-ink-200 hover:bg-ink-50' => $form['permalink_structure'] !== $key,
                            ])>
                                <input type="radio" wire:model.live="form.permalink_structure" value="{{ $key }}" class="border-ink-300 text-brand-600 focus:ring-brand-500">
                                <span class="w-36 text-sm font-bold">{{ $label }}</span>
                                <code class="min-w-0 truncate text-xs text-ink-500">{{ url('/'.($permalinks[$key] ?? 'news/{slug}')) }}</code>
                            </label>
                        @endforeach
                        <label @class([
                            'block cursor-pointer rounded-xl border p-3 transition',
                            'border-brand-500 bg-brand-50 ring-1 ring-brand-200' => $form['permalink_structure'] === 'custom',
                            'border-ink-100 hover:border-ink-200' => $form['permalink_structure'] !== 'custom',
                        ])>
                            <span class="flex items-center gap-3">
                                <input type="radio" wire:model.live="form.permalink_structure" value="custom" class="border-ink-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm font-bold">{{ __('Custom structure') }}</span>
                            </span>
                            <div class="mt-3 flex items-center rounded-lg border border-ink-200 bg-white focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500">
                                <span class="pl-3 text-xs text-ink-400">{{ url('/') }}</span>
                                <input type="text" wire:model="form.permalink_custom" class="min-w-0 flex-1 border-0 bg-transparent font-mono text-sm focus:ring-0" placeholder="/%category%/%postname%/">
                            </div>
                        </label>
                        @error('form.permalink_structure') <p class="text-sm text-brand-600">{{ $message }}</p> @enderror
                        @error('form.permalink_custom') <p class="text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="rounded-xl bg-ink-50 p-4">
                        <h3 class="text-sm font-bold">{{ __('Available tags') }}</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (['%year%', '%monthnum%', '%day%', '%postname%', '%category%', '%author%', '%post_id%'] as $tag)
                                <code class="rounded-md bg-white px-2 py-1 text-xs text-brand-700 ring-1 ring-ink-100">{{ $tag }}</code>
                            @endforeach
                        </div>
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
