<div>
    <h1 class="mb-1 text-2xl font-black">{{ __('Forgot password?') }}</h1>
    <p class="mb-6 text-sm text-ink-500">{{ __('Enter your email and we will send you a reset link.') }}</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    <form wire:submit="sendResetLink" class="space-y-4">
        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required autofocus
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-brand-600 px-4 py-2.5 font-bold text-white transition hover:bg-brand-700">
            {{ __('Email password reset link') }}
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-500">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">{{ __('Back to sign in') }}</a>
    </p>
</div>
