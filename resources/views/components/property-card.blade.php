@props(['property', 'eager' => false])

@php
    $accent = match ($property->status) {
        'for_sale'    => 'before:bg-teal-500',
        'under_offer' => 'before:bg-brass-500',
        default       => 'before:bg-ink-700',
    };
@endphp

<article @class([
    'card group relative flex flex-col transition-shadow duration-200 hover:shadow-lg hover:shadow-ink-900/5',
    'before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-1',
    $accent,
])>
    <a href="{{ route('properties.show', $property) }}" class="relative block overflow-hidden bg-ink-100">
        <img
            src="{{ $property->heroImageUrl() }}"
            alt="{{ $property->title }}, {{ $property->suburb }}"
            class="aspect-4/3 w-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="{{ $eager ? 'eager' : 'lazy' }}"
            width="800" height="600"
        >

        <x-status-chip :status="$property->status" class="absolute top-3 left-3 shadow-sm" />

        @if ($property->is_featured && $property->status !== 'sold')
            <span class="chip absolute top-3 right-3 bg-clay-600 text-white shadow-sm">
                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
                </svg>
                Featured
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <p @class([
            'text-lg font-semibold',
            'text-ink-900'   => $property->status !== 'sold',
            'text-teal-600'  => $property->status === 'sold',
        ])>{{ $property->priceDisplay() }}</p>

        <h3 class="mt-1 font-sans text-base font-semibold">
            <a href="{{ route('properties.show', $property) }}" class="hover:text-brass-600">{{ $property->title }}</a>
        </h3>

        <p class="mt-1 mb-4 text-sm text-ink-500">{{ $property->shortAddress() }}</p>

        <dl class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-ink-100 pt-4 text-sm text-ink-600">
            @if ($property->hasRooms())
                <div class="flex items-center gap-1.5">
                    <x-feature-icon name="bed" class="h-4.5 w-4.5 text-teal-500" />
                    <dt class="sr-only">Bedrooms</dt><dd>{{ $property->bedrooms }}</dd>
                </div>
                <div class="flex items-center gap-1.5">
                    <x-feature-icon name="bath" class="h-4.5 w-4.5 text-teal-500" />
                    <dt class="sr-only">Bathrooms</dt><dd>{{ $property->bathrooms }}</dd>
                </div>
                <div class="flex items-center gap-1.5">
                    <x-feature-icon name="car" class="h-4.5 w-4.5 text-teal-500" />
                    <dt class="sr-only">Car spaces</dt><dd>{{ $property->carspaces }}</dd>
                </div>
            @else
                <div class="flex items-center gap-1.5">
                    <dt class="sr-only">Property type</dt><dd>{{ $property->typeLabel() }}</dd>
                </div>
            @endif
            @if ($property->land_size)
                <div class="flex items-center gap-1.5">
                    <x-feature-icon name="land" class="h-4.5 w-4.5 text-brass-500" />
                    <dt class="sr-only">Land size</dt><dd>{{ $property->landDisplay() }}</dd>
                </div>
            @endif
        </dl>

        @if ($property->status === 'sold' && $property->sold_at)
            <p class="mt-3 text-xs font-medium text-ink-400">
                Sold {{ $property->sold_at->format('j M Y') }}@if ($property->days_on_market) &middot; {{ $property->days_on_market }} days on market @endif
            </p>
        @endif
    </div>
</article>
