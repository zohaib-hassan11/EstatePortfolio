@extends('layouts.app', [
    'nav' => $property->status === 'sold' ? 'sold' : 'properties',
    'title' => $property->title.', '.$property->suburb,
    'description' => $property->meta_description ?: Str::limit(strip_tags($property->description), 155),
    'image' => $property->heroImageUrl(),
    'imageAlt' => $property->title.', '.$property->suburb,
    'ogType' => 'article',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => $property->status === 'sold' ? 'Recently Sold' : 'For Sale',
         'url' => $property->status === 'sold' ? route('sold') : route('properties')],
        ['name' => $property->suburb, 'url' => null],
        ['name' => $property->title, 'url' => null],
    ],
    'schema' => [\App\Support\Seo::property($property)],
])

@php $agent = config('agent'); @endphp

@section('content')

<div class="container-page pt-6">
    <nav aria-label="Breadcrumb" class="text-sm text-ink-400">
        <ol class="flex flex-wrap items-center gap-2">
            <li><a href="{{ route('home') }}" class="hover:text-ink-900">Home</a></li>
            <li aria-hidden="true">/</li>
            <li>
                <a href="{{ $property->status === 'sold' ? route('sold') : route('properties') }}" class="hover:text-ink-900">
                    {{ $property->status === 'sold' ? 'Recently Sold' : 'For Sale' }}
                </a>
            </li>
            <li aria-hidden="true">/</li>
            <li class="text-ink-600" aria-current="page">{{ $property->suburb }}</li>
        </ol>
    </nav>
</div>

<section class="container-page pt-6">
    <x-island
        name="PropertyGallery"
        :props="[
            'title' => $property->title,
            'images' => $property->images->map(fn ($i) => ['url' => $i->url(), 'alt' => $i->alt])->values(),
        ]"
    >
        <img src="{{ $property->heroImageUrl() }}" alt="{{ $property->title }}"
             class="aspect-4/3 w-full rounded-card object-cover sm:aspect-16/9" width="1600" height="900">
    </x-island>
</section>

<section class="container-page py-10 lg:py-14">
    <div class="grid gap-12 lg:grid-cols-[1.6fr_1fr] lg:items-start">

        <div>
            <div class="flex flex-wrap items-center gap-3">
                <x-status-chip :status="$property->status" />
                <x-type-chip :type="$property->type" />
                @if ($property->is_featured && $property->status !== 'sold')
                    <span class="chip bg-clay-600 text-white">Featured</span>
                @endif
            </div>

            <h1 class="mt-4 text-3xl sm:text-4xl">{{ $property->title }}</h1>
            <p class="mt-2 text-lg text-ink-500">{{ $property->fullAddress() }}</p>
            <p @class([
                'mt-4 font-display text-3xl',
                'text-ink-900'  => $property->status !== 'sold',
                'text-teal-600' => $property->status === 'sold',
            ])>{{ $property->priceDisplay() }}</p>

            @if ($property->status === 'sold' && $property->sold_at)
                <p class="mt-2 text-sm text-ink-500">
                    Sold {{ $property->sold_at->format('j F Y') }}@if ($property->days_on_market) after {{ $property->days_on_market }} days on market @endif
                </p>
            @endif

            <dl class="mt-8 grid gap-4 rounded-card border border-teal-100 bg-teal-50/60 p-6 {{ $property->hasRooms() ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-2' }}">
                @php
                    $stats = $property->hasRooms()
                        ? [['bed', 'Bedrooms', $property->bedrooms],
                           ['bath', 'Bathrooms', $property->bathrooms],
                           ['car', 'Car spaces', $property->carspaces],
                           ['land', 'Land size', $property->landDisplay() ?? '—']]
                        : [['land', 'Land size', $property->landDisplay() ?? '—'],
                           ['land', 'Type', $property->typeLabel()]];
                @endphp
                @foreach ($stats as [$icon, $label, $value])
                    <div class="text-center">
                        <x-feature-icon :name="$icon" class="mx-auto h-6 w-6 text-teal-500" />
                        <dd class="mt-2 font-sans text-xl font-semibold text-ink-900">{{ $value }}</dd>
                        <dt class="text-xs tracking-wide text-ink-400 uppercase">{{ $label }}</dt>
                    </div>
                @endforeach
            </dl>

            <div class="prose-page mt-10">
                <h2 class="font-sans text-xl font-semibold text-ink-900">About this property</h2>
                @foreach (preg_split('/\n\s*\n/', trim($property->description)) as $paragraph)
                    <p class="mt-4">{{ $paragraph }}</p>
                @endforeach
            </div>

            @if (filled($property->features))
                <div class="mt-10">
                    <h2 class="font-sans text-xl font-semibold text-ink-900">Property features</h2>
                    <ul class="mt-4 grid gap-x-8 gap-y-2.5 sm:grid-cols-2">
                        @foreach ($property->features as $feature)
                            <li class="flex gap-3 text-[15px] text-ink-600">
                                <svg class="mt-0.5 h-4.5 w-4.5 shrink-0 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($property->inspection_times && $property->status !== 'sold')
                <div class="mt-10 rounded-card border-l-4 border-brass-500 bg-brass-50 p-6">
                    <h2 class="font-sans text-lg font-semibold text-ink-900">Inspection times</h2>
                    <p class="mt-2 text-[15px] text-ink-700">{{ $property->inspection_times }}</p>
                    <p class="mt-3 text-sm text-ink-600">
                        Can't make it? <a href="tel:{{ $agent['phone_dial'] }}" class="font-semibold underline">Call me</a> to arrange a private inspection.
                    </p>
                </div>
            @endif

            @if ($property->latitude && $property->longitude)
                <div class="mt-10">
                    <h2 class="font-sans text-xl font-semibold text-ink-900">Location</h2>
                    <div class="mt-4 overflow-hidden rounded-card border border-ink-100">
                        <iframe
                            src="https://maps.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}&output=embed&z=15"
                            class="aspect-16/9 w-full" style="border:0" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Map showing {{ $property->suburb }}"></iframe>
                    </div>
                    <p class="mt-2 text-xs text-ink-400">Map shows the approximate location of the property.</p>
                </div>
            @endif
        </div>

        {{-- Sticky enquiry rail --}}
        <aside class="lg:sticky lg:top-24">
            <div class="rounded-card border border-ink-100 bg-white p-6">
                <div class="flex items-center gap-4 border-b border-ink-100 pb-5">
                    <img src="{{ \App\Support\Media::agent('avatar') }}" alt="{{ $agent['name'] }}"
                         class="h-14 w-14 rounded-full object-cover" width="112" height="112">
                    <div>
                        <p class="font-sans font-semibold text-ink-900">{{ $agent['name'] }}</p>
                        <p class="text-sm text-ink-500">{{ $agent['title'] }}</p>
                    </div>
                </div>

                <div class="mt-5 space-y-2">
                    <a href="tel:{{ $agent['phone_dial'] }}" class="btn-primary w-full" data-analytics="click-to-call-listing">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.2.4 2.4.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1l-2.3 2.2Z"/>
                        </svg>
                        {{ $agent['phone'] }}
                    </a>
                    <a href="https://wa.me/{{ $agent['whatsapp'] }}?text={{ urlencode('Hi '.$agent['name'].", I'm interested in ".$property->title.' at '.$property->address.'.') }}"
                       target="_blank" rel="noopener noreferrer"
                       class="btn w-full bg-[#25D366] text-ink-900 hover:brightness-95">
                        <x-social-icon network="whatsapp" class="h-4 w-4" />
                        WhatsApp about this home
                    </a>
                </div>

                <div class="mt-6 border-t border-ink-100 pt-6">
                    @if (session('status'))
                        <p class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
                    @endif

                    <x-island
                        name="EnquiryForm"
                        :props="[
                            'endpoint' => route('enquiries.store'),
                            'type' => 'property',
                            'propertyId' => $property->id,
                            'propertyTitle' => $property->title.' at '.$property->address,
                            'heading' => 'Enquire about this property',
                            'submitLabel' => 'Send enquiry',
                            'compact' => true,
                        ]"
                    >
                        <h2 class="text-2xl">Enquire about this property</h2>
                        <noscript>
                            <form method="POST" action="{{ route('enquiries.store') }}" class="mt-4 space-y-3">
                                @csrf
                                <input type="hidden" name="type" value="property">
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <div><label class="label" for="p-name">Name</label><input id="p-name" class="input" name="name" required></div>
                                <div><label class="label" for="p-email">Email</label><input id="p-email" class="input" type="email" name="email" required></div>
                                <div><label class="label" for="p-phone">Phone</label><input id="p-phone" class="input" type="tel" name="phone"></div>
                                <div><label class="label" for="p-message">Message</label><textarea id="p-message" class="input" name="message" rows="4">I'd like more information about {{ $property->title }}.</textarea></div>
                                <button type="submit" class="btn-accent w-full">Send enquiry</button>
                            </form>
                        </noscript>
                    </x-island>
                </div>
            </div>
        </aside>
    </div>
</section>

@if ($similar->isNotEmpty())
    <section class="band-teal border-t border-teal-100">
        <div class="container-page py-16">
            <x-section-heading
                :eyebrow="$property->status === 'sold' ? 'More results' : 'You might also like'"
                :title="$property->status === 'sold' ? 'Other recent sales' : 'Similar properties'" />
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($similar as $other)
                    <x-property-card :property="$other" />
                @endforeach
            </div>
        </div>
    </section>
@endif

@endsection
