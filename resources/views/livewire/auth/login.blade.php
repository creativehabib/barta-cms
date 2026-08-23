<div>
    <h1 class="mb-1 text-2xl font-black">{{ __('Sign in') }}</h1>
    <p class="mb-6 text-sm text-ink-500">{{ __('Welcome back. Please enter your details.') }}</p>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required autofocus
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-semibold">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password" autocomplete="current-password" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('password') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input wire:model="remember" type="checkbox" class="rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                {{ __('Remember me') }}
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-semibold text-brand-600 hover:underline">
                {{ __('Forgot password?') }}
            </a>
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-brand-600 px-4 py-2.5 font-bold text-white transition hover:bg-brand-700"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">{{ __('Sign in') }}</span>
            <span wire:loading wire:target="login">{{ __('Signing in…') }}</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-500">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">{{ __('Create one') }}</a>
    </p>
</div>
