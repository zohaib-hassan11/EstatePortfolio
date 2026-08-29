@extends('layouts.app', [
    'nav' => 'contact',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Contact', 'url' => null],
    ],
    'title' => 'Contact',
    'description' => 'Call, WhatsApp or email '.config('agent.name').', or send a message and get a reply within one business day.',
])

@php $agent = config('agent'); @endphp

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-14 lg:py-20">
        <div class="max-w-2xl">
            <p class="eyebrow">Contact</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">Let's talk.</h1>
            <p class="prose-page mt-6">
                Call me directly, send a WhatsApp, or fill in the form and I'll reply within one business day.
                For anything urgent, the phone is always fastest.
            </p>
        </div>
    </div>
</section>

<section class="container-page py-16 lg:py-20">
    <div class="grid gap-12 lg:grid-cols-[1fr_1.1fr]">
        <div>
            <div class="space-y-4">
                <a href="tel:{{ $agent['phone_dial'] }}"
                   class="flex items-center gap-4 rounded-card border border-ink-100 bg-white p-5 transition hover:border-ink-900"
                   data-analytics="click-to-call-contact">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-ink-900 text-sand-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.2.4 2.4.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1l-2.3 2.2Z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm text-ink-500">Call or text</span>
                        <span class="block font-display text-2xl text-ink-900">{{ $agent['phone'] }}</span>
                    </span>
                </a>

                <a href="https://wa.me/{{ $agent['whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-4 rounded-card border border-ink-100 bg-white p-5 transition hover:border-[#25D366]">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#25D366] text-white">
                        <x-social-icon network="whatsapp" class="h-6 w-6" />
                    </span>
                    <span>
                        <span class="block text-sm text-ink-500">WhatsApp</span>
                        <span class="block font-sans text-lg font-semibold text-ink-900">Start a chat</span>
                    </span>
                </a>

                <a href="mailto:{{ $agent['email'] }}"
                   class="flex items-center gap-4 rounded-card border border-ink-100 bg-white p-5 transition hover:border-ink-900">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brass-500 text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 13l9-5.5M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                        </svg>
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm text-ink-500">Email</span>
                        <span class="block truncate font-sans text-lg font-semibold text-ink-900">{{ $agent['email'] }}</span>
                    </span>
                </a>
            </div>

            <div class="mt-8 rounded-card border border-ink-100 bg-white p-6">
                <h2 class="font-sans text-lg font-semibold">Office</h2>
                <address class="mt-3 text-[15px] leading-relaxed text-ink-600 not-italic">
                    {{ $agent['office']['street'] }}<br>
                    {{ $agent['office']['suburb'] }} {{ $agent['office']['state'] }} {{ $agent['office']['postcode'] }}<br>
                    <a href="tel:{{ preg_replace('/\s+/', '', $agent['office_phone']) }}" class="text-brass-600 hover:underline">{{ $agent['office_phone'] }}</a>
                </address>

                <h3 class="mt-6 font-sans text-sm font-semibold tracking-wide text-ink-900 uppercase">Hours</h3>
                <dl class="mt-3 space-y-2 text-[15px]">
                    @foreach ($agent['hours'] as $days => $hours)
                        <div class="flex justify-between gap-4">
                            <dt class="text-ink-600">{{ $days }}</dt>
                            <dd class="text-ink-500">{{ $hours }}</dd>
                        </div>
                    @endforeach
                </dl>

                <div class="mt-6 flex gap-2">
                    @foreach ($agent['social'] as $network => $url)
                        @continue(blank($url))
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-100 text-ink-600 transition hover:bg-ink-900 hover:text-sand-50"
                           aria-label="{{ ucfirst($network) }}">
                            <x-social-icon :network="$network" class="h-4 w-4" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-card border border-ink-100 bg-white p-6 sm:p-8">
            @if (session('status'))
                <p class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
            @endif

            <x-island
                name="EnquiryForm"
                :props="[
                    'endpoint' => route('enquiries.store'),
                    'type' => 'contact',
                    'heading' => 'Send a message',
                    'subheading' => 'Tell me what you need and I will come back to you within one business day.',
                    'submitLabel' => 'Send message',
                ]"
            >
                <h2 class="text-3xl">Send a message</h2>
                <p class="mt-2 text-ink-500">Tell me what you need and I will come back to you within one business day.</p>
                <noscript>
                    <form method="POST" action="{{ route('enquiries.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="contact">
                        <div><label class="label" for="c-name">Your name</label><input id="c-name" class="input" name="name" required></div>
                        <div><label class="label" for="c-email">Email</label><input id="c-email" class="input" type="email" name="email" required></div>
                        <div><label class="label" for="c-phone">Phone</label><input id="c-phone" class="input" type="tel" name="phone"></div>
                        <div><label class="label" for="c-message">Message</label><textarea id="c-message" class="input" name="message" rows="5"></textarea></div>
                        <button type="submit" class="btn-accent w-full">Send message</button>
                    </form>
                </noscript>
            </x-island>
        </div>
    </div>
</section>

<section class="container-page pb-20">
    <x-island
        name="ServiceAreaMap"
        :props="[
            'areas' => $agent['service_areas'],
            'embedQuery' => $agent['office']['street'].', '.$agent['office']['suburb'].' '.$agent['office']['state'].' '.$agent['office']['postcode'],
            'officeLabel' => $agent['office']['street'].', '.$agent['office']['suburb'],
            'directionsUrl' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($agent['office']['street'].', '.$agent['office']['suburb'].' '.$agent['office']['state'].' '.$agent['office']['postcode']),
        ]"
    >
        <h2 class="text-3xl">Service areas</h2>
        <p class="prose-page mt-4">{{ implode(', ', $agent['service_areas']) }}</p>
    </x-island>
</section>

@endsection
