<?php

use Illuminate\Support\Str;

if (! function_exists('setting')) {
    /** Fetch a CMS setting value (cached) with a fallback default. */
    function setting(string $key, mixed $default = null): mixed
    {
        return app('barta.settings')->get($key, $default);
    }
}

if (! function_exists('barta_locales')) {
    /** List of enabled locale codes, e.g. ['bn','en']. */
    function barta_locales(): array
    {
        return config('barta.locales', ['bn', 'en']);
    }
}

if (! function_exists('locale_name')) {
    function locale_name(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return config("barta.locale_names.$locale", strtoupper($locale));
    }
}

if (! function_exists('active_theme')) {
    function active_theme(): string
    {
        return app('barta.theme')->active();
    }
}

if (! function_exists('theme_asset')) {
    function theme_asset(string $path): string
    {
        return app('barta.theme')->asset($path);
    }
}

if (! function_exists('reading_time')) {
    /** Estimated reading time in minutes for a block of (HTML) text. */
    function reading_time(?string $text): int
    {
        $words = str_word_count(strip_tags((string) $text));
        $wpm = max(1, (int) config('barta.words_per_minute', 200));

        return max(1, (int) ceil($words / $wpm));
    }
}

if (! function_exists('to_bn_number')) {
    /** Convert ASCII digits to Bengali numerals (used when locale = bn). */
    function to_bn_number(string|int|null $value): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($en, $bn, (string) $value);
    }
}

if (! function_exists('localized_number')) {
    /** Format a number, switching to Bengali digits when the locale is bn. */
    function localized_number(string|int|float|null $value): string
    {
        $value = (string) $value;

        return app()->getLocale() === 'bn' ? to_bn_number($value) : $value;
    }
}

if (! function_exists('money')) {
    /** Format an amount with the configured payment currency symbol. */
    function money(float|int $amount, ?string $currency = null): string
    {
        $currency ??= config('barta.payments.currency', 'BDT');
        $symbols = ['BDT' => '৳', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'INR' => '₹'];
        $symbol = $symbols[$currency] ?? ($currency.' ');
        $formatted = number_format((float) $amount, 2);

        return $symbol.(app()->getLocale() === 'bn' ? to_bn_number($formatted) : $formatted);
    }
}

if (! function_exists('excerpt')) {
    function excerpt(?string $text, int $words = 30): string
    {
        return Str::words(trim(strip_tags((string) $text)), $words, ' …');
    }
}
