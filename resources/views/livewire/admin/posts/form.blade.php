<div class="mx-auto max-w-6xl">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.posts.index') }}" class="text-ink-400 hover:text-ink-700">←</a>
            <h1 class="text-2xl font-black">{{ $post ? __('Edit post') : __('New post') }}</h1>
        </div>

        {{-- Locale switcher --}}
        <div class="inline-flex rounded-lg border border-ink-200 bg-white p-1">
            @foreach ($locales as $loc)
                <button type="button" wire:click="$set('activeLocale', '{{ $loc }}')"
                        @class([
                            'rounded-md px-3 py-1 text-sm font-bold',
                            'bg-brand-600 text-white' => $activeLocale === $loc,
                            'text-ink-600' => $activeLocale !== $loc,
                        ])>{{ locale_name($loc) }}</button>
            @endforeach
        </div>
    </div>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        {{-- Main column --}}
        <div class="space-y-5 lg:col-span-2">
            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <label class="mb-1 block text-sm font-semibold">{{ __('Title') }} ({{ locale_name($activeLocale) }})</label>
                <input type="text" wire:model="title.{{ $activeLocale }}"
                       class="w-full rounded-lg border-ink-200 text-lg font-bold focus:border-brand-500 focus:ring-brand-500"
                       placeholder="{{ __('Enter the headline…') }}">
                @error('title.'.config('barta.default_locale')) <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <button type="button" wire:click="aiSuggestTitles" wire:loading.attr="disabled" wire:target="aiSuggestTitles"
                            class="rounded-md bg-ink-100 px-2.5 py-1 text-xs font-semibold text-ink-700 hover:bg-ink-200">
                        ✦ {{ __('AI headlines') }}
                    </button>
                    <button type="button" wire:click="aiTranslate('{{ $activeLocale === 'bn' ? 'en' : 'bn' }}')" wire:loading.attr="disabled"
                            class="rounded-md bg-ink-100 px-2.5 py-1 text-xs font-semibold text-ink-700 hover:bg-ink-200">
                        ✦ {{ __('Translate to') }} {{ $activeLocale === 'bn' ? 'English' : 'বাংলা' }}
                    </button>
                    <span wire:loading class="text-xs text-ink-400">{{ __('AI working…') }}</span>
                </div>

                @error('ai') <p class="mt-2 rounded bg-brand-50 px-2 py-1 text-sm text-brand-700">{{ $message }}</p> @enderror
                @if ($aiMessage) <p class="mt-2 text-sm text-green-600">{{ $aiMessage }}</p> @endif

                @if ($aiTitleOptions)
                    <ul class="mt-2 space-y-1 rounded-lg border border-ink-100 p-2">
                        @foreach ($aiTitleOptions as $opt)
                            <li>
                                <button type="button" wire:click="useTitle(@js($opt))"
                                        class="block w-full rounded px-2 py-1 text-left text-sm hover:bg-brand-50">{{ $opt }}</button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <div class="mb-1 flex items-center justify-between">
                    <label class="text-sm font-semibold">{{ __('Body') }} ({{ locale_name($activeLocale) }})</label>
                </div>
                <textarea wire:model="body.{{ $activeLocale }}" rows="16"
                          class="w-full rounded-lg border-ink-200 font-serif leading-relaxed focus:border-brand-500 focus:ring-brand-500"
                          placeholder="{{ __('Write the article… (HTML allowed)') }}"></textarea>
                <p class="mt-1 text-xs text-ink-400">{{ __('Tip: paste HTML or write plain paragraphs. A rich editor plugin can be enabled later.') }}</p>
            </div>

            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <label class="mb-1 block text-sm font-semibold">{{ __('Excerpt') }} ({{ locale_name($activeLocale) }})</label>
                <textarea wire:model="excerpt.{{ $activeLocale }}" rows="3"
                          class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500"></textarea>
                <button type="button" wire:click="aiSummarize" wire:loading.attr="disabled" wire:target="aiSummarize"
                        class="mt-2 rounded-md bg-ink-100 px-2.5 py-1 text-xs font-semibold text-ink-700 hover:bg-ink-200">
                    ✦ {{ __('AI summary') }}
                </button>
            </div>

            {{-- SEO --}}
            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <h3 class="mb-3 font-bold">{{ __('SEO') }}</h3>
                <label class="mb-1 block text-sm font-semibold">{{ __('Meta title') }}</label>
                <input type="text" wire:model="metaTitle.{{ $activeLocale }}" class="mb-3 w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                <label class="mb-1 block text-sm font-semibold">{{ __('Meta description') }}</label>
                <textarea wire:model="metaDescription.{{ $activeLocale }}" rows="2" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500"></textarea>
                <label class="mb-1 mt-3 block text-sm font-semibold">{{ __('Slug') }}</label>
                <input type="text" wire:model="slug" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="{{ __('auto-generated') }}">
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <h3 class="mb-3 font-bold">{{ __('Publish') }}</h3>
                <label class="mb-1 block text-sm font-semibold">{{ __('Status') }}</label>
                <select wire:model="status" class="mb-3 w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                    <option value="draft">{{ __('Draft') }}</option>
                    <option value="pending">{{ __('Pending review') }}</option>
                    <option value="scheduled">{{ __('Scheduled') }}</option>
                    <option value="published">{{ __('Published') }}</option>
                </select>

                <label class="mb-1 block text-sm font-semibold">{{ __('Publish date') }}</label>
                <input type="datetime-local" wire:model="published_at" class="mb-3 w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">

                <label class="mb-1 block text-sm font-semibold">{{ __('Type') }}</label>
                <select wire:model="type" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                    <option value="post">{{ __('Post') }}</option>
                    <option value="page">{{ __('Page') }}</option>
                </select>

                <div class="mt-4 flex gap-2">
                    <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2 font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    <button type="button" wire:click="save(true)" class="rounded-lg border border-ink-200 px-3 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Publish') }}</button>
                </div>
                @if ($post)
                    <a href="{{ $post->url() }}" target="_blank" class="mt-2 block text-center text-sm font-semibold text-brand-600 hover:underline">{{ __('View post') }} ↗</a>
                @endif
            </div>

            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <h3 class="mb-3 font-bold">{{ __('Featured image') }}</h3>
                @if ($cover)
                    <img src="{{ $cover->temporaryUrl() }}" class="mb-2 aspect-video w-full rounded-lg object-cover">
                @elseif ($existingCover && ! $removeCover)
                    <img src="{{ $existingCover }}" class="mb-2 aspect-video w-full rounded-lg object-cover">
                @endif
                <input type="file" wire:model="cover" accept="image/*" class="w-full text-sm">
                <div wire:loading wire:target="cover" class="mt-1 text-xs text-ink-400">{{ __('Uploading…') }}</div>
                @error('cover') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                @if ($existingCover)
                    <label class="mt-2 flex items-center gap-2 text-sm text-ink-600">
                        <input type="checkbox" wire:model="removeCover" class="rounded border-ink-300 text-brand-600"> {{ __('Remove current image') }}
                    </label>
                @endif
            </div>

            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <h3 class="mb-3 font-bold">{{ __('Organise') }}</h3>
                <label class="mb-1 block text-sm font-semibold">{{ __('Category') }}</label>
                <select wire:model="category_id" class="mb-3 w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                    <option value="">{{ __('— none —') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->getTranslation('name', $activeLocale, false) ?: $cat->name }}</option>
                    @endforeach
                </select>

                <label class="mb-1 block text-sm font-semibold">{{ __('Tags') }}</label>
                <input type="text" wire:model="tagsInput" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="{{ __('comma, separated') }}">
                <button type="button" wire:click="aiTags" class="mt-2 rounded-md bg-ink-100 px-2.5 py-1 text-xs font-semibold text-ink-700 hover:bg-ink-200">✦ {{ __('AI tags') }}</button>
            </div>

            <div class="rounded-xl border border-ink-100 bg-white p-5">
                <h3 class="mb-3 font-bold">{{ __('Options') }}</h3>
                <div class="space-y-2 text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_featured" class="rounded border-ink-300 text-brand-600"> {{ __('Featured') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_breaking" class="rounded border-ink-300 text-brand-600"> {{ __('Breaking news') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" wire:model="is_premium" class="rounded border-ink-300 text-brand-600"> {{ __('Premium (paywall)') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" wire:model="allow_comments" class="rounded border-ink-300 text-brand-600"> {{ __('Allow comments') }}</label>
                </div>

                <label class="mb-1 mt-4 block text-sm font-semibold">{{ __('Video URL') }}</label>
                <input type="url" wire:model="video_url" class="mb-3 w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                <label class="mb-1 block text-sm font-semibold">{{ __('Source') }}</label>
                <input type="text" wire:model="source" class="mb-2 w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                <input type="url" wire:model="source_url" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="https://">
            </div>
        </div>
    </form>
</div>
