{{-- Top utility bar: date, language switch, auth links. --}}
<div class="border-b border-ink-100 bg-ink-50 text-xs text-ink-500">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-2">
        <div class="hidden sm:block">
            @php($today = now())
            {{ localized_number($today->translatedFormat('j F Y')) }}
        </div>

        <div class="flex items-center gap-4">
            {{-- Language switch --}}
            <div class="flex items-center gap-1">
                @foreach (barta_locales() as $loc)
                    <a href="{{ url('/lang/'.$loc) }}"
                       @class([
                           'rounded px-2 py-0.5 font-semibold transition',
                           'bg-brand-600 text-white' => app()->getLocale() === $loc,
                           'hover:bg-ink-100' => app()->getLocale() !== $loc,
                       ])>{{ locale_name($loc) }}</a>
                @endforeach
            </div>

            <span class="text-ink-200">|</span>

            {{-- Auth --}}
            @auth
                <a href="{{ route('account') }}" class="font-semibold hover:text-brand-600">{{ __('My account') }}</a>
                @if (auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="font-semibold hover:text-brand-600">{{ __('Dashboard') }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-semibold hover:text-brand-600">{{ __('Log out') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="font-semibold hover:text-brand-600">{{ __('Sign in') }}</a>
                <a href="{{ route('register') }}" class="rounded bg-brand-600 px-2 py-0.5 font-semibold text-white hover:bg-brand-700">{{ __('Create account') }}</a>
            @endauth
        </div>
    </div>
</div>
