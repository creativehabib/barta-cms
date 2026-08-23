<div class="mx-auto max-w-6xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Menus') }}</h1>
        <button wire:click="createMenu" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New menu') }}</button>
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        {{-- Menu list --}}
        <div class="rounded-xl border border-ink-100 bg-white p-4">
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-ink-500">{{ __('Your menus') }}</h3>
            <div class="space-y-1">
                @forelse ($menus as $m)
                    <div @class([
                        'flex items-center justify-between rounded-lg px-3 py-2 text-sm',
                        'bg-brand-50 text-brand-700' => $m->id === $selectedMenuId,
                        'hover:bg-ink-50' => $m->id !== $selectedMenuId,
                    ])>
                        <button wire:click="selectMenu({{ $m->id }})" class="flex-1 text-left font-semibold">
                            {{ $m->name }}
                            <span class="ml-1 rounded bg-ink-100 px-1.5 text-xs font-mono text-ink-500">{{ $m->location }}</span>
                        </button>
                        <button wire:click="deleteMenu({{ $m->id }})" wire:confirm="{{ __('Delete this menu and its items?') }}" class="ml-2 text-ink-400 hover:text-brand-600">&times;</button>
                    </div>
                @empty
                    <p class="text-sm text-ink-400">{{ __('No menus yet.') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Items --}}
        <div class="lg:col-span-2 rounded-xl border border-ink-100 bg-white p-4">
            @if ($menu)
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-ink-500">{{ __('Items in') }} “{{ $menu->name }}”</h3>
                    <button wire:click="createItem" class="rounded-lg border border-ink-200 px-3 py-1.5 text-sm font-semibold hover:bg-ink-50">+ {{ __('Add item') }}</button>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-ink-100 text-xs uppercase tracking-wide text-ink-500">
                        <tr>
                            <th class="py-2">{{ __('Label') }}</th>
                            <th class="py-2">{{ __('Type') }}</th>
                            <th class="py-2">{{ __('Pos') }}</th>
                            <th class="py-2 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-50">
                        @forelse ($items as $item)
                            <tr>
                                <td class="py-2 font-semibold">
                                    @if ($item->parent_id) <span class="text-ink-400">↳ </span>@endif
                                    {{ $item->getTranslation('label', app()->getLocale(), false) ?: $item->label }}
                                </td>
                                <td class="py-2 text-ink-500">{{ __(ucfirst($item->type)) }}</td>
                                <td class="py-2 text-ink-500">{{ to_bn_number($item->position) }}</td>
                                <td class="py-2 text-right">
                                    <button wire:click="editItem({{ $item->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                                    <button wire:click="deleteItem({{ $item->id }})" wire:confirm="{{ __('Delete this item?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-ink-400">{{ __('No items. Add one to build the menu.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <p class="py-8 text-center text-ink-400">{{ __('Create a menu to get started.') }}</p>
            @endif
        </div>
    </div>

    {{-- Menu modal --}}
    @if ($showMenuModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showMenuModal', false)">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ __('New menu') }}</h2>
                <form wire:submit="saveMenu" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Name') }}</label>
                        <input type="text" wire:model="menuName" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('menuName') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Location') }}</label>
                        <input type="text" wire:model="menuLocation" list="menu-locations" placeholder="primary, footer, mobile…" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500">
                        <datalist id="menu-locations">
                            <option value="primary"></option>
                            <option value="footer"></option>
                            <option value="mobile"></option>
                        </datalist>
                        @error('menuLocation') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showMenuModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Item modal --}}
    @if ($showItemModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showItemModal', false)">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingItemId ? __('Edit item') : __('Add item') }}</h2>
                <form wire:submit="saveItem" class="space-y-3">
                    @foreach (barta_locales() as $loc)
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Label') }} ({{ locale_name($loc) }})</label>
                            <input type="text" wire:model="itemLabel.{{ $loc }}" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('itemLabel.'.$loc) <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Type') }}</label>
                            <select wire:model.live="itemType" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="custom">{{ __('Custom link') }}</option>
                                <option value="category">{{ __('Category') }}</option>
                                <option value="page">{{ __('Page') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Opens in') }}</label>
                            <select wire:model="itemTarget" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="_self">{{ __('Same tab') }}</option>
                                <option value="_blank">{{ __('New tab') }}</option>
                            </select>
                        </div>
                    </div>

                    @if ($itemType === 'custom')
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('URL') }}</label>
                            <input type="text" wire:model="itemUrl" placeholder="https:// or /path" class="w-full rounded-lg border-ink-200 font-mono text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    @elseif ($itemType === 'category')
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Category') }}</label>
                            <select wire:model="itemTargetId" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('— select —') }}</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif ($itemType === 'page')
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Page') }}</label>
                            <select wire:model="itemTargetId" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('— select —') }}</option>
                                @foreach ($pages as $page)
                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Parent') }}</label>
                            <select wire:model="itemParentId" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">{{ __('— top level —') }}</option>
                                @foreach ($items as $it)
                                    @if ($it->id !== $editingItemId)
                                        <option value="{{ $it->id }}">{{ $it->label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Position') }}</label>
                            <input type="number" wire:model="itemPosition" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showItemModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
