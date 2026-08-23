<div class="mx-auto max-w-7xl px-4 py-10">
    @php
        $loc = app()->getLocale();
    @endphp

    {{-- Heading --}}
    <div class="text-center">
        <h1 class="text-3xl font-black sm:text-4xl">{{ app()->getLocale() === 'bn' ? 'সাবস্ক্রিপশন প্ল্যান' : 'Subscription plans' }}</h1>
        <p class="mx-auto mt-2 max-w-xl text-ink-500">
            {{ app()->getLocale() === 'bn' ? 'একটি প্ল্যান বেছে নিয়ে প্রিমিয়াম সংবাদ ও বিশ্লেষণ পড়ুন।' : 'Choose a plan to unlock premium news and analysis.' }}
        </p>
    </div>

    {{-- Flash messages --}}
    @if (session('error'))
        <div class="mx-auto mt-6 max-w-2xl rounded-lg bg-red-50 px-4 py-3 text-center text-sm font-semibold text-red-700">{{ session('error') }}</div>
    @endif
    @if (session('status'))
        <div class="mx-auto mt-6 max-w-2xl rounded-lg bg-green-50 px-4 py-3 text-center text-sm font-semibold text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Active subscription banner --}}
    @if ($currentSubscription && $currentSubscription->isActive())
        <div class="mx-auto mt-6 max-w-2xl rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-center">
            <p class="font-bold text-green-800">
                {{ app()->getLocale() === 'bn' ? 'আপনার একটি সক্রিয় সাবস্ক্রিপশন রয়েছে।' : 'You have an active subscription.' }}
            </p>
            <a href="{{ route('account') }}" class="mt-1 inline-block text-sm font-semibold text-green-700 underline">
                {{ app()->getLocale() === 'bn' ? 'অ্যাকাউন্ট দেখুন' : 'View account' }}
            </a>
        </div>
    @endif

    {{-- Plan cards --}}
    @if ($plans->isNotEmpty())
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($plans as $plan)
                @php
                    $count = (int) $plan->interval_count;
                    $intervalLabel = match ($plan->interval) {
                        'lifetime' => app()->getLocale() === 'bn' ? 'একবার (আজীবন)' : 'one-time (lifetime)',
                        'year' => app()->getLocale() === 'bn' ? ($count > 1 ? localized_number($count).' বছর' : 'প্রতি বছর') : ($count > 1 ? 'per '.$count.' years' : 'per year'),
                        'week' => app()->getLocale() === 'bn' ? ($count > 1 ? localized_number($count).' সপ্তাহ' : 'প্রতি সপ্তাহ') : ($count > 1 ? 'per '.$count.' weeks' : 'per week'),
                        'day' => app()->getLocale() === 'bn' ? localized_number($count).' দিন' : 'per '.$count.' days',
                        default => app()->getLocale() === 'bn' ? ($count > 1 ? localized_number($count).' মাস' : 'প্রতি মাস') : ($count > 1 ? 'per '.$count.' months' : 'per month'),
                    };
                    $featured = $loop->index === 1; // highlight the middle plan
                @endphp

                <div @class([
                    'relative flex flex-col rounded-2xl border bg-white p-6 shadow-sm',
                    'border-brand-600 ring-2 ring-brand-600' => $featured,
                    'border-ink-200' => ! $featured,
                ])>
                    @if ($featured)
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-brand-600 px-3 py-1 text-xs font-bold text-white">
                            {{ app()->getLocale() === 'bn' ? 'জনপ্রিয়' : 'Popular' }}
                        </span>
                    @endif

                    <h2 class="text-lg font-black">{{ $plan->getTranslation('name', $loc, false) }}</h2>

                    <div class="mt-3 flex items-baseline gap-1">
                        <span class="text-3xl font-black text-brand-600">{{ money($plan->price, $plan->currency) }}</span>
                        <span class="text-sm text-ink-400">/ {{ $intervalLabel }}</span>
                    </div>

                    @if ($desc = $plan->getTranslation('description', $loc, false))
                        <p class="mt-3 text-sm text-ink-500">{{ $desc }}</p>
                    @endif

                    @if (is_array($plan->features) && count($plan->features))
                        <ul class="mt-4 space-y-2 text-sm text-ink-700">
                            @foreach ($plan->features as $feature)
                                <li class="flex items-start gap-2">
                                    <span class="mt-0.5 text-green-600">✓</span>
                                    <span>{{ is_array($feature) ? ($feature[$loc] ?? reset($feature)) : $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-6 flex-1"></div>

                    {{-- CTA --}}
                    @auth
                        @if ($currentSubscription && $currentSubscription->isActive())
                            <button type="button" disabled class="w-full rounded-lg bg-ink-100 px-4 py-2.5 text-sm font-bold text-ink-400">
                                {{ app()->getLocale() === 'bn' ? 'ইতিমধ্যে সাবস্ক্রাইব করা' : 'Already subscribed' }}
                            </button>
                        @else
                            <form method="POST" action="{{ route('checkout', $plan) }}" class="space-y-2">
                                @csrf
                                @if (count($gateways) > 1)
                                    <label class="block text-xs font-semibold text-ink-500">{{ app()->getLocale() === 'bn' ? 'পেমেন্ট মাধ্যম' : 'Payment method' }}</label>
                                    <select name="gateway" class="w-full rounded-lg border-ink-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                        @foreach ($gateways as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="gateway" value="{{ array_key_first($gateways) }}">
                                @endif
                                <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-brand-700">
                                    {{ app()->getLocale() === 'bn' ? 'সাবস্ক্রাইব করুন' : 'Subscribe' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full rounded-lg bg-brand-600 px-4 py-2.5 text-center text-sm font-bold text-white hover:bg-brand-700">
                            {{ app()->getLocale() === 'bn' ? 'সাবস্ক্রাইব করতে সাইন ইন করুন' : 'Sign in to subscribe' }}
                        </a>
                    @endauth
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-10 rounded-xl border border-dashed border-ink-200 py-16 text-center text-ink-400">
            {{ app()->getLocale() === 'bn' ? 'এখনো কোনো প্ল্যান নেই।' : 'No plans available yet.' }}
        </p>
    @endif

    @if (count($gateways))
        <p class="mt-8 text-center text-xs text-ink-400">
            {{ app()->getLocale() === 'bn' ? 'নিরাপদ পেমেন্ট:' : 'Secure payment via' }} {{ implode(' · ', $gateways) }}
        </p>
    @endif
</div>
