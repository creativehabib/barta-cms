<div class="mx-auto max-w-6xl space-y-5">
    <h1 class="text-2xl font-black">{{ __('Media Library') }}</h1>

    {{-- Uploader --}}
    <div class="rounded-xl border border-dashed border-ink-200 bg-white p-6 text-center">
        <label class="cursor-pointer">
            <input type="file" wire:model="uploads" multiple class="hidden">
            <div class="text-sm font-semibold text-brand-600">{{ __('Click to upload files') }}</div>
            <div class="mt-1 text-xs text-ink-400">{{ __('Images and documents up to 20 MB each') }}</div>
        </label>
        <div wire:loading wire:target="uploads" class="mt-2 text-xs text-ink-400">{{ __('Uploading…') }}</div>
        @error('uploads.*') <p class="mt-2 text-sm text-brand-600">{{ $message }}</p> @enderror
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
        @forelse ($files as $file)
            <div class="group overflow-hidden rounded-xl border border-ink-100 bg-white" x-data="{ copied: false }">
                <div class="flex aspect-square items-center justify-center bg-ink-50">
                    @if ($file['is_image'])
                        <img src="{{ $file['url'] }}" class="h-full w-full object-cover" alt="{{ $file['name'] }}">
                    @else
                        <span class="text-3xl text-ink-300">📄</span>
                    @endif
                </div>
                <div class="p-2">
                    <div class="truncate text-xs font-semibold" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                    <div class="text-xs text-ink-400">{{ to_bn_number(number_format($file['size'] / 1024, 0)) }} KB</div>
                    <div class="mt-1 flex items-center justify-between">
                        <button type="button"
                                x-on:click="navigator.clipboard.writeText('{{ $file['url'] }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                class="text-xs font-semibold text-brand-600 hover:underline">
                            <span x-show="!copied">{{ __('Copy URL') }}</span>
                            <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                        </button>
                        <button wire:click="delete('{{ $file['path'] }}')" wire:confirm="{{ __('Delete this file?') }}" class="text-xs text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-ink-200 p-10 text-center text-ink-400">{{ __('No media yet. Upload something above.') }}</div>
        @endforelse
    </div>

    <p class="text-xs text-ink-400">{{ __('Tip: run "php artisan storage:link" once so uploaded files are publicly served.') }}</p>
</div>
