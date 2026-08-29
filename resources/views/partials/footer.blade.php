@php $agent = config('agent'); @endphp

<footer class="mt-24 bg-ink-900 text-ink-200">
    <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-brass-500 to-clay-500" aria-hidden="true"></div>
    <div class="container-page py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <img src="{{ \App\Support\Media::agent('logo_light') }}" alt="{{ $agent['agency'] }}" width="230" height="55" class="h-14 w-auto">
                <p class="mt-3 text-sm text-ink-400">{{ $agent['name'] }} &middot; {{ $agent['title'] }}</p>
                <p class="mt-4 text-sm leading-relaxed text-ink-300">{{ $agent['tagline'] }}</p>

                <div class="mt-5 flex gap-2">
                    @foreach ($agent['social'] as $network => $url)
                        @continue(blank($url))
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                           class="flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-brass-500"
                           aria-label="{{ ucfirst($network) }}">
                            <x-social-icon :network="$network" class="h-4 w-4" />
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-[0.14em] text-white uppercase">Explore</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('properties') }}" class="text-ink-300 hover:text-white">Properties for Sale</a></li>
                    <li><a href="{{ route('sold') }}" class="text-ink-300 hover:text-white">Recently Sold</a></li>
                    <li><a href="{{ route('buying') }}" class="text-ink-300 hover:text-white">Buying</a></li>
                    <li><a href="{{ route('selling') }}" class="text-ink-300 hover:text-white">Selling &amp; Free Appraisal</a></li>
                    <li><a href="{{ route('testimonials') }}" class="text-ink-300 hover:text-white">Testimonials</a></li>
                    <li><a href="{{ route('about') }}" class="text-ink-300 hover:text-white">About</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-[0.14em] text-white uppercase">Get in touch</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>
                        <a href="tel:{{ $agent['phone_dial'] }}" class="text-ink-300 hover:text-white" data-analytics="click-to-call-footer">
                            {{ $agent['phone'] }} <span class="text-ink-500">(mobile)</span>
                        </a>
                    </li>
                    <li><a href="tel:{{ preg_replace('/\s+/', '', $agent['office_phone']) }}" class="text-ink-300 hover:text-white">{{ $agent['office_phone'] }} <span class="text-ink-500">(office)</span></a></li>
                    <li><a href="mailto:{{ $agent['email'] }}" class="text-ink-300 hover:text-white">{{ $agent['email'] }}</a></li>
                    <li class="pt-1 text-ink-300">
                        {{ $agent['office']['street'] }}<br>
                        {{ $agent['office']['suburb'] }} {{ $agent['office']['state'] }} {{ $agent['office']['postcode'] }}
                    </li>
                </ul>
                <a href="https://wa.me/{{ $agent['whatsapp'] }}" target="_blank" rel="noopener noreferrer"
                   class="mt-4 inline-flex items-center gap-2 rounded-full bg-[#25D366] px-4 py-2 text-sm font-semibold text-ink-900">
                    <x-social-icon network="whatsapp" class="h-4 w-4" />
                    WhatsApp me
                </a>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-[0.14em] text-white uppercase">Office hours</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($agent['hours'] as $days => $hours)
                        <li class="flex justify-between gap-4 text-ink-300">
                            <span>{{ $days }}</span>
                            <span class="text-right text-ink-400">{{ $hours }}</span>
                        </li>
                    @endforeach
                </ul>

                <h3 class="mt-6 text-sm font-semibold tracking-[0.14em] text-white uppercase">Service areas</h3>
                <p class="mt-3 text-sm leading-relaxed text-ink-400">{{ implode(', ', $agent['service_areas']) }}</p>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-ink-400 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $agent['agency'] }}.@if (filled($agent['license'])) {{ $agent['license'] }}. @endif</p>
            <p class="flex gap-4">
                <a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a>
                <a href="{{ route('sitemap') }}" class="hover:text-white">Sitemap</a>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Admin</a>
            </p>
        </div>
    </div>
</footer>
