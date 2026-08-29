@extends('layouts.app', [
    'nav' => 'about',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'About', 'url' => null],
    ],
    'title' => 'About '.config('agent.name'),
    'description' => 'Meet '.config('agent.name').', a '.config('agent.title').' working across DHA, Bahria Town, Gulberg and Model Town in Lahore.',
])

@php $agent = config('agent'); @endphp

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page grid gap-12 py-14 lg:grid-cols-[1fr_1.1fr] lg:items-center lg:py-20">
        <img
            src="{{ \App\Support\Media::agent('photo') }}"
            alt="Portrait of {{ $agent['name'] }}"
            class="w-full rounded-card object-cover shadow-xl shadow-ink-900/10"
            width="720" height="880"
        >

        <div>
            <p class="eyebrow">About the agent</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">{{ $agent['name'] }}</h1>
            <p class="mt-2 text-lg text-ink-500">{{ $agent['title'] }} &middot; {{ $agent['agency'] }}</p>

            <div class="prose-page mt-6">
                @foreach ($agent['bio'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="tel:{{ $agent['phone_dial'] }}" class="btn-primary" data-analytics="click-to-call-about">Call {{ $agent['phone'] }}</a>
                <a href="{{ route('contact') }}" class="btn-outline">Send a message</a>
            </div>

            <p class="mt-6 text-sm text-ink-400">{{ $agent['license'] }}</p>
        </div>
    </div>
</section>

<section class="container-page py-20">
    <dl class="grid gap-8 sm:grid-cols-3">
        <x-stat :value="$agent['properties_sold'].'+'" label="Transfers handled across Lahore" />
        <x-stat :value="$agent['avg_days_on_market'].' days'" label="Average time on market" />
        <x-stat :value="count($agent['service_areas']).' areas'" label="Covered in detail, week in week out" />
    </dl>
</section>

<section class="band-cream border-y border-brass-200/70">
    <div class="container-page py-20">
        <x-section-heading
            eyebrow="How I work"
            title="Three commitments I make to every client"
            align="center" />

        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['I price on evidence, not optimism', 'A valuation that flatters you costs you money later. I show you the recent transfers the number came from, and I explain the ones that do not support it.'],
                ['I check the file before you pay', 'Transfer letter, NOC, society dues, possession status. I check all of it myself before any token money changes hands, on either side of the deal.'],
                ['I tell you the awkward part', 'If the dues are behind, if the price expectation is high, if the file has a problem - you hear it from me first, not from the society office.'],
            ] as $i => [$heading, $body])
                @php $tone = [['bg-teal-50 border-teal-200', 'text-teal-500'],
                              ['bg-brass-50 border-brass-200', 'text-brass-500'],
                              ['bg-clay-50 border-clay-200', 'text-clay-500']][$i]; @endphp
                <div class="rounded-card border p-8 {{ $tone[0] }}">
                    <span class="font-display text-4xl {{ $tone[1] }}">0{{ $i + 1 }}</span>
                    <h3 class="mt-4 font-sans text-lg font-semibold">{{ $heading }}</h3>
                    <p class="mt-3 text-[15px] leading-relaxed text-ink-500">{{ $body }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if ($testimonials->isNotEmpty())
    <section class="container-page py-20">
        <x-section-heading eyebrow="In their words" title="What clients say" align="center" />

        <div class="mx-auto mt-12 grid max-w-4xl gap-6 md:grid-cols-2">
            @foreach ($testimonials as $testimonial)
                <blockquote class="card p-8">
                    <div class="flex gap-1" aria-label="{{ $testimonial->rating }} out of 5 stars">
                        @for ($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="h-4 w-4 text-brass-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-4 text-[15px] leading-relaxed text-ink-600">&ldquo;{{ $testimonial->body }}&rdquo;</p>
                    <footer class="mt-4 text-sm">
                        <span class="font-semibold text-ink-900">{{ $testimonial->author }}</span>
                        <span class="text-ink-400">&middot; {{ $testimonial->role }}, {{ $testimonial->location }}</span>
                    </footer>
                </blockquote>
            @endforeach
        </div>

        <p class="mt-10 text-center">
            <a href="{{ route('testimonials') }}" class="btn-outline">Read more testimonials</a>
        </p>
    </section>
@endif

@endsection
