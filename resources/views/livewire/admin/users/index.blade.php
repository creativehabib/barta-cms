<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">{{ __('Users') }}</h1>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('New user') }}</button>
    </div>

    <div class="flex flex-wrap gap-3 rounded-xl border border-ink-100 bg-white p-4">
        <input type="search" wire:model.live.debounce.400ms="search" placeholder="{{ __('Search name, email, username…') }}"
               class="min-w-48 flex-1 rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
        <select wire:model.live="role" class="rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">{{ __('All roles') }}</option>
            @foreach ($allRoles as $r)
                <option value="{{ $r }}">{{ __(ucfirst(str_replace('-', ' ', $r))) }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('User') }}</th>
                    <th class="px-4 py-3">{{ __('Roles') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($users as $user)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatarUrl() }}" class="h-9 w-9 rounded-full object-cover" alt="">
                                <div>
                                    <div class="font-semibold">{{ $user->name }}</div>
                                    <div class="text-xs text-ink-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @foreach ($user->getRoleNames() as $roleName)
                                <span class="mr-1 inline-block rounded bg-ink-100 px-2 py-0.5 text-xs font-semibold text-ink-600">{{ ucfirst(str_replace('-', ' ', $roleName)) }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $user->is_active,
                                'bg-ink-100 text-ink-600' => ! $user->is_active,
                            ])>{{ $user->is_active ? __('Active') : __('Disabled') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $user->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                            @if ($user->id !== auth()->id())
                                <button wire:click="delete({{ $user->id }})" wire:confirm="{{ __('Delete this user?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-10 text-center text-ink-400">{{ __('No users found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit user') : __('New user') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Name') }}</label>
                            <input type="text" wire:model="name" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('name') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Username') }}</label>
                            <input type="text" wire:model="username" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                            @error('username') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
                        <input type="email" wire:model="email" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Password') }} @if ($editingId)<span class="font-normal text-ink-400">({{ __('leave blank to keep') }})</span>@endif</label>
                        <input type="password" wire:model="password" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('password') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Phone') }}</label>
                            <input type="text" wire:model="phone" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold">{{ __('Language') }}</label>
                            <select wire:model="locale" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach (barta_locales() as $loc)
                                    <option value="{{ $loc }}">{{ locale_name($loc) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Bio') }}</label>
                        <textarea wire:model="bio" rows="2" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Website') }}</label>
                        <input type="url" wire:model="website" placeholder="https://example.com" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('website') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Roles') }}</label>
                        <div class="flex flex-wrap gap-3 rounded-lg border border-ink-100 p-3 text-sm">
                            @foreach ($allRoles as $r)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" wire:model="roles" value="{{ $r }}" class="rounded border-ink-300 text-brand-600">
                                    {{ ucfirst(str_replace('-', ' ', $r)) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="is_active" class="rounded border-ink-300 text-brand-600"> {{ __('Active account') }}
                    </label>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
