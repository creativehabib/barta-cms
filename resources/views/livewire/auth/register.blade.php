<div>
    <h1 class="mb-1 text-2xl font-black">{{ __('Create your account') }}</h1>
    <p class="mb-6 text-sm text-ink-500">{{ __('Join to comment, save articles and subscribe.') }}</p>

    <form wire:submit="register" class="space-y-4">
        <div>
            <label for="name" class="mb-1 block text-sm font-semibold">{{ __('Full name') }}</label>
            <input wire:model="name" id="name" type="text" autocomplete="name" required autofocus
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('name') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-semibold">{{ __('Password') }}</label>
            <input wire:model="password" id="password" type="password" autocomplete="new-password" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('password') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-semibold">{{ __('Confirm password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-brand-600 px-4 py-2.5 font-bold text-white transition hover:bg-brand-700"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">{{ __('Create account') }}</span>
            <span wire:loading wire:target="register">{{ __('Creating…') }}</span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-ink-500">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">{{ __('Sign in') }}</a>
    </p>
</div>
