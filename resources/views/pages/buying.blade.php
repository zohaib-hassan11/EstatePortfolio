@extends('layouts.app', [
    'nav' => 'buying',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Buying', 'url' => null],
    ],
    'title' => 'Buying a home',
    'description' => 'What to expect when you buy through '.config('agent.name').' - viewings, file checks, token money, transfer and possession, explained step by step.',
])

@php $agent = config('agent'); @endphp

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-14 lg:py-20">
        <div class="max-w-3xl">
            <p class="eyebrow">Buying</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">Buy with someone who checks the file before you pay.</h1>
            <p class="prose-page mt-6">
                Most dealers work for the seller, and on my own listings so do I &mdash; but that is no
                reason to leave you guessing. You will get a straight answer about the transfer letter, the
                society dues, the possession status and whether the file is clean, before any money moves.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('properties') }}" class="btn-accent">Browse current listings</a>
                <a href="tel:{{ $agent['phone_dial'] }}" class="btn-outline" data-analytics="click-to-call-buying">Call {{ $agent['phone'] }}</a>
            </div>
        </div>
    </div>
</section>

<section class="container-page py-20">
    <x-section-heading eyebrow="The process" title="From first inspection to keys in hand" />

    <ol class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['Sort your funds or financing first', 'Whether it is cash or a bank facility, know your ceiling before you start looking. A buyer who can actually move gets taken seriously, and often gets a better price for it.'],
            ['Visit twice, at different times', 'Come once with me and once on your own, at a different time of day. Street traffic, water pressure and how much sun the lounge gets all change through the day.'],
            ['Check the file', 'Transfer or allotment letter, NOC from the society, dues cleared, possession status. I check these in person and show you what I found.'],
            ['Agree the price and pay token', 'Token money is paid against a written receipt setting out the price, the timeline, and what happens if either side walks away. Never on a handshake.'],
            ['Clear dues and taxes', 'Outstanding society dues, the transfer fee, and FBR withholding tax - which is far lower if you are on the active taxpayer list.'],
            ['Transfer and possession', 'Transfer is completed at the society or registry office with both parties present. Keys follow the same day unless you have agreed otherwise in writing.'],
        ] as $i => [$heading, $body])
@php
                $stepTones = ['bg-teal-500', 'bg-brass-500', 'bg-clay-500', 'bg-indigo-500', 'bg-teal-600', 'bg-brass-600'];
            @endphp
            <li class="card p-8">
                <span class="flex h-9 w-9 items-center justify-center rounded-full {{ $stepTones[$i % count($stepTones)] }} font-sans text-sm font-bold text-white">{{ $i + 1 }}</span>
                <h3 class="mt-4 font-sans text-lg font-semibold">{{ $heading }}</h3>
                <p class="mt-2 text-[15px] leading-relaxed text-ink-500">{{ $body }}</p>
            </li>
        @endforeach
    </ol>
</section>

<section class="band-cream border-y border-brass-200/70">
    <div class="container-page py-20">
        <x-section-heading eyebrow="Questions I get asked" title="Buyer FAQs" />

        <div class="mt-10 max-w-3xl divide-y divide-ink-200">
            @foreach ([
                ['How do I know the file is clean?', 'For a society property: the allotment or transfer letter in the seller\'s name, a dues clearance from the society office, and nothing recorded against the plot number. For older areas, the registry and fard from the revenue office. I check these in person, not from a photo on WhatsApp.'],
                ['How much is token money, and can I get it back?', 'Usually one to five percent, and whether you get it back depends entirely on what the receipt says. Put the agreed price, the deadline and the forfeit terms in writing. A verbal understanding is worth nothing in a dispute.'],
                ['What will the transfer actually cost me?', 'Society transfer fee, stamp duty and registration where it applies, and FBR withholding tax - roughly double if you are not on the active taxpayer list. Getting filer status sorted first is the cheapest saving available to you.'],
                ['Can I buy from overseas?', 'Yes, through a power of attorney attested at the Pakistani consulate in your country. Get it drafted before you need it - this is where most overseas purchases stall.'],
            ] as [$question, $answer])
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-sans text-lg font-semibold text-ink-900">
                        {{ $question }}
                        <svg class="h-5 w-5 shrink-0 text-ink-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                        </svg>
                    </summary>
                    <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-ink-500">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@if ($listings->isNotEmpty())
    <section class="container-page py-20">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading eyebrow="Available now" title="Currently for sale" />
            <a href="{{ route('properties') }}" class="btn-outline shrink-0">View all</a>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($listings as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </section>
@endif

@endsection
