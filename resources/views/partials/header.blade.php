@php
    $agent = config('agent');
    $links = [
        ['key' => 'home',         'label' => 'Home',          'url' => route('home')],
        ['key' => 'about',        'label' => 'About',         'url' => route('about')],
        ['key' => 'properties',   'label' => 'For Sale',      'url' => route('properties')],
        ['key' => 'sold',         'label' => 'Recently Sold', 'url' => route('sold')],
        ['key' => 'buying',       'label' => 'Buying',        'url' => route('buying')],
        ['key' => 'selling',      'label' => 'Selling',       'url' => route('selling')],
        ['key' => 'testimonials', 'label' => 'Testimonials',  'url' => route('testimonials')],
        ['key' => 'contact',      'label' => 'Contact',       'url' => route('contact')],
    ];
    $current = $nav ?? '';
@endphp

{{--
    Utility strip. Carries the secondary details so the main bar only has to hold
    the logo, the nav and the one call to action. Scrolls away; the nav below is
    what stays pinned.
--}}
<div class="hidden bg-ink-900 text-ink-300 xl:block">
    <div class="container-wide flex h-10 items-center justify-between gap-6 text-xs">
        <div class="flex items-center gap-6 whitespace-nowrap">
            <span class="flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5 text-brass-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>
                </svg>
                {{ $agent['office']['street'] }}, {{ $agent['office']['suburb'] }}
            </span>
            <a href="mailto:{{ $agent['email'] }}" class="flex items-center gap-1.5 transition-colors hover:text-white">
                <svg class="h-3.5 w-3.5 text-brass-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 12 13l9-5.5M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                </svg>
                {{ $agent['email'] }}
            </a>
        </div>

        <div class="flex items-center gap-4 whitespace-nowrap">
            <div class="flex items-center gap-2.5">
                @foreach ($agent['social'] as $network => $url)
                    @continue(blank($url))
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       class="text-ink-400 transition-colors hover:text-white" aria-label="{{ ucfirst($network) }}">
                        <x-social-icon :network="$network" class="h-3.5 w-3.5" />
                    </a>
                @endforeach
            </div>

            <span class="h-3.5 w-px bg-white/15" aria-hidden="true"></span>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 font-medium transition-colors hover:text-white">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                Admin
            </a>
        </div>
    </div>
</div>

<header class="sticky top-0 z-30 border-b border-ink-100 bg-sand-50/95 backdrop-blur">
    <div class="container-wide flex h-18 items-center justify-between gap-8">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="{{ $agent['agency'] }} home">
            <img src="{{ \App\Support\Media::agent('logo_mark') }}" alt="" width="40" height="40" class="h-10 w-10 shrink-0" aria-hidden="true">
            <span class="leading-tight whitespace-nowrap">
                <span class="block font-display text-xl text-ink-900">{{ $agent['agency'] }}</span>
                <span class="block text-[10px] font-semibold tracking-[0.14em] text-ink-400 uppercase">{{ $agent['title'] }}</span>
            </span>
        </a>

        <nav class="hidden min-w-0 xl:block" aria-label="Main">
            <ul class="flex items-center gap-5 text-sm 2xl:gap-6">
                @foreach ($links as $link)
                    <li>
                        <a
                            href="{{ $link['url'] }}"
                            @class([
                                'relative whitespace-nowrap font-medium transition-colors hover:text-ink-900',
                                'text-ink-900 after:absolute after:-bottom-1.5 after:left-0 after:h-0.5 after:w-full after:rounded-full after:bg-brass-500' => $current === $link['key'],
                                'text-ink-500' => $current !== $link['key'],
                            ])
                            @if ($current === $link['key']) aria-current="page" @endif
                        >{{ $link['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <a href="tel:{{ $agent['phone_dial'] }}"
               class="hidden items-center gap-2 rounded-full bg-ink-900 px-5 py-2.5 text-sm font-semibold whitespace-nowrap text-sand-50 transition-colors hover:bg-ink-700 xl:inline-flex"
               data-analytics="click-to-call-header">
                <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.2.4 2.4.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1A17 17 0 0 1 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.4 0 .8-.2 1l-2.3 2.2Z"/>
                </svg>
                {{ $agent['phone'] }}
            </a>

            <x-island
                name="MobileNav"
                :props="[
                    'links' => $links,
                    'phone' => $agent['phone'],
                    'phoneDial' => $agent['phone_dial'],
                    'appraisalUrl' => route('selling').'#appraisal',
                    'adminUrl' => route('admin.dashboard'),
                    'current' => $current,
                ]"
            >
                {{-- Fallback before Vue mounts: a plain link to the contact page --}}
                <a href="{{ route('contact') }}" class="flex h-10 w-10 items-center justify-center rounded-lg text-ink-900 xl:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" d="M3.5 7h17M3.5 12h17M3.5 17h17"/>
                    </svg>
                </a>
            </x-island>
        </div>
    </div>
</header>
