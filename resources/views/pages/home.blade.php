@extends('layouts.app', ['nav' => 'home'])

@php $agent = config('agent'); @endphp

@section('content')

{{-- Hero --}}
<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page grid gap-12 py-14 lg:grid-cols-[1.15fr_1fr] lg:items-center lg:py-20">
        <div>
            <p class="eyebrow">{{ $agent['title'] }} &middot; {{ $agent['office']['suburb'] }}</p>

            <h1 class="mt-4 text-4xl leading-[1.08] sm:text-5xl lg:text-6xl">
                Straight dealing on<br class="hidden sm:block"> Lahore property, and a<br class="hidden sm:block"> clean file every time.
            </h1>

            <p class="prose-page mt-6 max-w-xl">
                I'm {{ $agent['name'] }}, a property dealer working across DHA, Bahria Town, Gulberg and
                Model Town. I check the file before you commit, I tell you what the dues and the transfer
                will actually cost, and I answer the phone.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('selling') }}#appraisal" class="btn-accent">Get a free appraisal</a>
                <a href="{{ route('properties') }}" class="btn-outline">Browse properties for sale</a>
            </div>

            <dl class="mt-12 grid grid-cols-3 gap-6 border-t border-ink-200 pt-8">
                <x-stat :value="$agent['properties_sold'].'+'" label="Homes sold" />
                <x-stat :value="$agent['avg_days_on_market']" label="Avg days on market" />
                <x-stat :value="$agent['experience_years']" label="Years selling locally" />
            </dl>
        </div>

        <div class="relative">
            <img
                src="{{ \App\Support\Media::agent('photo') }}"
                alt="{{ $agent['name'] }}, {{ $agent['title'] }} at {{ $agent['agency'] }}"
                class="w-full rounded-card object-cover shadow-xl shadow-ink-900/10"
                width="720" height="880"
                loading="eager"
            >
            <div class="absolute -bottom-5 left-5 right-5 rounded-xl border border-ink-100 bg-white/95 p-4 shadow-lg backdrop-blur sm:left-8 sm:right-auto sm:w-72">
                <p class="text-sm font-semibold text-ink-900">Prefer to just talk it through?</p>
                <a href="tel:{{ $agent['phone_dial'] }}"
                   class="mt-2 flex items-center gap-2 font-display text-2xl text-ink-900 hover:text-brass-600"
                   data-analytics="click-to-call-hero">
                    <svg class="h-5 w-5 text-brass-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.2.4 2.4.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1l-2.3 2.2Z"/>
                    </svg>
                    {{ $agent['phone'] }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Featured listings --}}
<section class="container-page py-20">
    <div class="flex flex-wrap items-end justify-between gap-6">
        <x-section-heading
            eyebrow="On the market"
            title="Properties for sale"
            lead="Every listing here is one I have walked myself, priced from recent transfers in the same block - not from a number designed to win the listing." />
        <a href="{{ route('properties') }}" class="btn-outline shrink-0">View all listings</a>
    </div>

    @if ($featured->isEmpty())
        <div class="mt-10">
            <x-empty-state
                title="No listings right now"
                message="New campaigns launch most weeks. Register your interest and I'll send them before they hit the portals."
                :action-url="route('contact')"
                action-label="Register your interest" />
        </div>
    @else
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $property)
                <x-property-card :property="$property" :eager="$loop->first" />
            @endforeach
        </div>
    @endif
</section>

{{-- Services split --}}
<section class="bg-ink-900 text-sand-100">
    <div class="container-page py-20">
        <div class="max-w-2xl">
            <p class="eyebrow text-brass-300">How I can help</p>
            <h2 class="mt-3 text-3xl text-white sm:text-4xl">Two jobs, done properly.</h2>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            <div class="rounded-card border border-teal-400/25 bg-teal-500/10 p-8">
                <span class="chip mb-4 bg-teal-400/20 text-teal-200">Sellers</span>
                <h3 class="font-sans text-xl font-semibold text-white">Selling</h3>
                <p class="mt-3 text-ink-300">
                    A price set from recent transfers in your own block, listings placed where your buyer
                    actually looks, and a weekly update so you always know where things stand.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-ink-200">
                    @foreach (['Free, no-obligation market appraisal', 'Professional photographs included', 'Weekly written updates', 'Negotiation and transfer handled by me'] as $item)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('selling') }}" class="btn-ghost-light mt-8">Selling &amp; free appraisal</a>
            </div>

            <div class="rounded-card border border-clay-400/25 bg-clay-500/10 p-8">
                <span class="chip mb-4 bg-clay-400/20 text-clay-200">Buyers</span>
                <h3 class="font-sans text-xl font-semibold text-white">Buying</h3>
                <p class="mt-3 text-ink-300">
                    Early word on what is coming up, honest answers about what is wrong with a property as
                    well as what is right, and the file checked before you pay any token money.
                </p>
                <ul class="mt-6 space-y-3 text-sm text-ink-200">
                    @foreach (['Off-market and pre-launch alerts', 'File, NOC and society dues checked before you pay', 'Viewings arranged around your schedule', 'Introductions to trusted bankers and lawyers'] as $item)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-teal-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('buying') }}" class="btn-ghost-light mt-8">Buying with me</a>
            </div>
        </div>
    </div>
</section>

{{-- Recently sold --}}
@if ($recentlySold->isNotEmpty())
    <section class="band-teal border-y border-teal-100">
    <div class="container-page py-20">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading
                eyebrow="Results"
                title="Recently sold"
                :lead="'Recent transfers I have handled across Lahore. Here are the latest.'" />
            <a href="{{ route('sold') }}" class="btn-outline shrink-0">See all sales</a>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($recentlySold as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </div>
    </section>
@endif

{{-- Testimonials --}}
@if ($testimonials->isNotEmpty())
    <section class="band-cream border-y border-brass-200/70">
        <div class="container-page py-20">
            <x-island
                class="mx-auto max-w-3xl"
                name="TestimonialCarousel"
                :props="['items' => $testimonials->map(fn ($t) => [
                    'body' => $t->body, 'author' => $t->author, 'location' => $t->location,
                    'role' => $t->role, 'rating' => $t->rating,
                ])]"
            >
                {{-- Server-rendered fallback, replaced by the carousel on hydrate --}}
                <blockquote class="text-center">
                    <p class="font-display text-2xl leading-snug text-ink-900 sm:text-3xl">&ldquo;{{ $testimonials->first()->body }}&rdquo;</p>
                    <footer class="mt-6 text-sm text-ink-500">
                        <span class="font-semibold text-ink-800">{{ $testimonials->first()->author }}</span>
                    </footer>
                </blockquote>
            </x-island>

            <p class="mt-10 text-center">
                <a href="{{ route('testimonials') }}" class="text-sm font-semibold text-brass-600 underline hover:text-brass-700">
                    Read all {{ $testimonials->count() > 1 ? 'testimonials' : 'reviews' }}
                </a>
            </p>
        </div>
    </section>
@endif

{{-- Service area map --}}
<section class="container-page py-20">
    <x-island
        name="ServiceAreaMap"
        :props="[
            'areas' => $agent['service_areas'],
            'embedQuery' => $agent['office']['suburb'].' '.$agent['office']['state'].' Australia',
            'officeLabel' => $agent['office']['street'].', '.$agent['office']['suburb'],
            'directionsUrl' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($agent['office']['street'].', '.$agent['office']['suburb'].' '.$agent['office']['state'].' '.$agent['office']['postcode']),
        ]"
    >
        <h2 class="text-3xl">Service areas</h2>
        <p class="prose-page mt-4">{{ implode(', ', $agent['service_areas']) }}</p>
    </x-island>
</section>

{{-- Closing CTA --}}
<section class="container-page pb-20">
    <div class="grid gap-10 rounded-card bg-gradient-to-br from-ink-900 via-ink-800 to-teal-900 p-8 text-sand-100 sm:p-12 lg:grid-cols-2 lg:items-center">
        <div>
            <p class="eyebrow text-brass-300">No obligation</p>
            <h2 class="mt-3 text-3xl text-white sm:text-4xl">What is your home actually worth?</h2>
            <p class="mt-4 text-ink-300">
                I'll visit the property, pull the recent transfers from your own block, and give you a
                written range with the reasoning attached. If the answer is "wait six months", I'll say so.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="tel:{{ $agent['phone_dial'] }}" class="btn-accent" data-analytics="click-to-call-cta">Call {{ $agent['phone'] }}</a>
                <a href="https://wa.me/{{ $agent['whatsapp'] }}" target="_blank" rel="noopener noreferrer" class="btn-ghost-light">WhatsApp instead</a>
            </div>
        </div>

        <div class="rounded-xl bg-sand-50 p-6 text-ink-800 sm:p-8">
            <x-island
                name="EnquiryForm"
                :props="[
                    'endpoint' => route('enquiries.store'),
                    'type' => 'appraisal',
            'types' => config('agent.property_types'),
                    'heading' => 'Book your free appraisal',
                    'submitLabel' => 'Request my appraisal',
                    'compact' => true,
                ]"
            >
                <p class="font-display text-2xl">Book your free appraisal</p>
                <p class="mt-2 text-sm text-ink-500">
                    <noscript>Please call {{ $agent['phone'] }} or email {{ $agent['email'] }}.</noscript>
                </p>
            </x-island>
        </div>
    </div>
</section>

@endsection
