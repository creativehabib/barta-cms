{{-- Premium paywall shown in place of the article body for non-subscribers. --}}
<div class="relative">
    {{-- Fade-out teaser --}}
    <div class="pointer-events-none absolute inset-x-0 -top-24 h-24 bg-gradient-to-b from-transparent to-white"></div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-8 text-center">
        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-amber-400 text-2xl text-amber-950">★</div>
        <h3 class="mt-4 text-xl font-black text-ink-900">
            {{ app()->getLocale() === 'bn' ? 'এটি প্রিমিয়াম কনটেন্ট' : 'This is premium content' }}
        </h3>
        <p class="mx-auto mt-2 max-w-md text-sm text-ink-600">{{ __('This content requires an active subscription.') }}</p>

        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('plans') }}" class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-700">
                {{ __('Subscription plans') }}
            </a>
            @guest
                <a href="{{ route('login') }}" class="rounded-lg border border-ink-300 px-5 py-2.5 text-sm font-bold text-ink-700 hover:bg-white">
                    {{ __('Sign in') }}
                </a>
            @endguest
        </div>
    </div>
</div>
