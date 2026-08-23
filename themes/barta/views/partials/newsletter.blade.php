{{-- Newsletter opt-in box. Params: $heading (optional) --}}
@php($heading = $heading ?? __('Newsletters'))
<div class="rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 p-5 text-white">
    <h3 class="text-lg font-black">{{ $heading }}</h3>
    <p class="mt-1 text-sm text-white/80">{{ setting('newsletter_pitch', __('Get the day\'s top stories in your inbox.')) }}</p>

    @if (session('newsletter_status'))
        <p class="mt-3 rounded-lg bg-white/15 px-3 py-2 text-sm">{{ session('newsletter_status') }}</p>
    @endif

    <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-3 space-y-2">
        @csrf
        <input type="email" name="email" required placeholder="{{ __('Email') }}"
               class="w-full rounded-lg border-0 px-3 py-2 text-sm text-ink-900 focus:ring-2 focus:ring-white/60">
        @error('email')<p class="text-xs text-amber-200">{{ $message }}</p>@enderror
        <button type="submit" class="w-full rounded-lg bg-white px-3 py-2 text-sm font-bold text-brand-700 hover:bg-brand-50">
            {{ app()->getLocale() === 'bn' ? 'সাবস্ক্রাইব করুন' : 'Subscribe' }}
        </button>
    </form>
</div>
