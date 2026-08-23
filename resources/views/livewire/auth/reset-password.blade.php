<div>
    <h1 class="mb-1 text-2xl font-black">{{ __('Reset password') }}</h1>
    <p class="mb-6 text-sm text-ink-500">{{ __('Choose a new password for your account.') }}</p>

    <form wire:submit="resetPassword" class="space-y-4">
        <div>
            <label for="email" class="mb-1 block text-sm font-semibold">{{ __('Email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('email') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm font-semibold">{{ __('New password') }}</label>
            <input wire:model="password" id="password" type="password" autocomplete="new-password" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
            @error('password') <p class="mt-1 text-sm text-brand-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1 block text-sm font-semibold">{{ __('Confirm new password') }}</label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password" autocomplete="new-password" required
                   class="w-full rounded-lg border-ink-200 focus:border-brand-500 focus:ring-brand-500">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-brand-600 px-4 py-2.5 font-bold text-white transition hover:bg-brand-700">
            {{ __('Reset password') }}
        </button>
    </form>
</div>
