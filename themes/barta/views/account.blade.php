@extends('theme::layouts.app')

@section('content')
@php
    $loc = app()->getLocale();
@endphp
<div class="mx-auto max-w-4xl px-4 py-8">

    <h1 class="text-3xl font-black">{{ app()->getLocale() === 'bn' ? 'আমার অ্যাকাউন্ট' : 'My account' }}</h1>

    {{-- Profile --}}
    <section class="mt-6 flex items-center gap-4 rounded-2xl border border-ink-100 bg-white p-6">
        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover">
        <div>
            <p class="text-xl font-black">{{ $user->name }}</p>
            <p class="text-sm text-ink-500">{{ $user->email }}</p>
        </div>
        @if ($user->isStaff())
            <a href="{{ route('admin.dashboard') }}" class="ml-auto rounded-lg bg-ink-900 px-4 py-2 text-sm font-bold text-white hover:bg-ink-700">
                {{ app()->getLocale() === 'bn' ? 'ড্যাশবোর্ড' : 'Dashboard' }}
            </a>
        @endif
    </section>

    {{-- Subscription --}}
    <section class="mt-6">
        <h2 class="mb-3 text-lg font-black">{{ app()->getLocale() === 'bn' ? 'সাবস্ক্রিপশন' : 'Subscription' }}</h2>
        @if ($subscription && $subscription->isActive())
            <div class="rounded-2xl border border-green-200 bg-green-50 p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-green-700">{{ __('Active') }}</p>
                        <p class="mt-1 text-xl font-black">{{ optional($subscription->plan)->getTranslation('name', $loc, false) }}</p>
                    </div>
                    <div class="text-right text-sm text-ink-600">
                        @if ($subscription->ends_at)
                            <p>{{ app()->getLocale() === 'bn' ? 'মেয়াদ শেষ:' : 'Renews / expires:' }}</p>
                            <p class="font-bold">{{ localized_number($subscription->ends_at->translatedFormat('j F Y')) }}</p>
                        @else
                            <p class="font-bold">{{ app()->getLocale() === 'bn' ? 'আজীবন' : 'Lifetime' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-ink-200 bg-ink-50 p-6 text-center">
                <p class="text-ink-600">{{ app()->getLocale() === 'bn' ? 'আপনার কোনো সক্রিয় সাবস্ক্রিপশন নেই।' : 'You have no active subscription.' }}</p>
                <a href="{{ route('plans') }}" class="mt-3 inline-block rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-700">
                    {{ __('Subscription plans') }}
                </a>
            </div>
        @endif
    </section>

    {{-- Payment history --}}
    <section class="mt-6">
        <h2 class="mb-3 text-lg font-black">{{ app()->getLocale() === 'bn' ? 'পেমেন্ট ইতিহাস' : 'Payment history' }}</h2>
        @if ($payments->isNotEmpty())
            <div class="overflow-x-auto rounded-2xl border border-ink-100">
                <table class="min-w-full divide-y divide-ink-100 text-sm">
                    <thead class="bg-ink-50 text-left text-xs uppercase tracking-wide text-ink-400">
                        <tr>
                            <th class="px-4 py-3 font-bold">{{ __('Date') }}</th>
                            <th class="px-4 py-3 font-bold">{{ __('Gateway') }}</th>
                            <th class="px-4 py-3 font-bold">{{ __('Amount') }}</th>
                            <th class="px-4 py-3 font-bold">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink-100">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="px-4 py-3 text-ink-600">{{ localized_number(optional($payment->created_at)->translatedFormat('j M Y')) }}</td>
                                <td class="px-4 py-3 font-semibold capitalize">{{ $payment->gateway }}</td>
                                <td class="px-4 py-3 font-bold">{{ money($payment->amount, $payment->currency) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $map = ['success' => 'bg-green-100 text-green-700', 'pending' => 'bg-amber-100 text-amber-700', 'failed' => 'bg-red-100 text-red-700'];
                                    @endphp
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold @class([$map[$payment->status] ?? 'bg-ink-100 text-ink-600'])">
                                        {{ __(ucfirst($payment->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="rounded-xl border border-dashed border-ink-200 py-10 text-center text-ink-400">{{ app()->getLocale() === 'bn' ? 'কোনো পেমেন্ট নেই।' : 'No payments yet.' }}</p>
        @endif
    </section>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <button type="submit" class="rounded-lg border border-ink-300 px-5 py-2.5 text-sm font-bold text-ink-700 hover:bg-ink-50">
            {{ app()->getLocale() === 'bn' ? 'লগ আউট' : 'Log out' }}
        </button>
    </form>
</div>
@endsection
