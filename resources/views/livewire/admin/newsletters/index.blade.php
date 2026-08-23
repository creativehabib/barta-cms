<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black">{{ __('Newsletters') }}</h1>
            <p class="text-sm text-ink-400">{{ to_bn_number($subscriberCount) }} {{ __('active subscribers') }}</p>
        </div>
        <button wire:click="create" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">+ {{ __('Compose') }}</button>
    </div>

    <div class="overflow-hidden rounded-xl border border-ink-100 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-ink-100 bg-ink-50 text-xs uppercase tracking-wide text-ink-500">
                <tr>
                    <th class="px-4 py-3">{{ __('Subject') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th class="px-4 py-3">{{ __('Recipients') }}</th>
                    <th class="px-4 py-3">{{ __('Sent') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-50">
                @forelse ($newsletters as $newsletter)
                    <tr class="hover:bg-ink-50/50">
                        <td class="px-4 py-3 font-semibold">{{ $newsletter->subject }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $newsletter->status === 'sent',
                                'bg-blue-100 text-blue-700' => $newsletter->status === 'sending',
                                'bg-amber-100 text-amber-700' => $newsletter->status === 'draft',
                            ])>{{ __(ucfirst($newsletter->status ?? 'draft')) }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-500">{{ $newsletter->recipients ? to_bn_number($newsletter->recipients) : '—' }}</td>
                        <td class="px-4 py-3 text-ink-500">{{ $newsletter->sent_at?->format('d M Y, H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($newsletter->status !== 'sent')
                                <button wire:click="edit({{ $newsletter->id }})" class="font-semibold text-brand-600 hover:underline">{{ __('Edit') }}</button>
                                <button wire:click="send({{ $newsletter->id }})" wire:confirm="{{ __('Send this newsletter to all subscribers?') }}" class="ml-2 font-semibold text-green-600 hover:underline">{{ __('Send') }}</button>
                            @endif
                            <button wire:click="delete({{ $newsletter->id }})" wire:confirm="{{ __('Delete this newsletter?') }}" class="ml-2 text-ink-400 hover:text-brand-600">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-ink-400">{{ __('No newsletters yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $newsletters->links() }}</div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click.self="$set('showModal', false)">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-black">{{ $editingId ? __('Edit newsletter') : __('Compose newsletter') }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Subject') }}</label>
                        <input type="text" wire:model="subject" class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
                        @error('subject') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">{{ __('Content (HTML allowed)') }}</label>
                        <textarea wire:model="content" rows="10" class="w-full rounded-lg border-ink-200 font-serif focus:border-brand-500 focus:ring-brand-500"></textarea>
                        @error('content') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="$set('showModal', false)" class="rounded-lg border border-ink-200 px-4 py-2 text-sm font-semibold hover:bg-ink-50">{{ __('Cancel') }}</button>
                        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white hover:bg-brand-700">{{ __('Save draft') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
