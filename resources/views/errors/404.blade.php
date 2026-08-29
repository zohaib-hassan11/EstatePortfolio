@extends('layouts.app', [
    'title' => 'Page not found',
    'description' => 'That page is no longer here. Browse current listings or get in touch.',
    'robots' => 'noindex, follow',
])

@section('content')
<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-20 text-center lg:py-28">
        <p class="eyebrow">404</p>
        <h1 class="mt-4 text-4xl sm:text-5xl">That page has moved on.</h1>
        <p class="prose-page mx-auto mt-5 max-w-xl">
            The listing may have sold, or the address may be mistyped. Everything currently
            on the market is one click away.
        </p>

        <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('properties') }}" class="btn-accent">Browse properties for sale</a>
            <a href="{{ route('contact') }}" class="btn-outline">Get in touch</a>
        </div>
    </div>
</section>

<section class="container-page py-16">
    <x-section-heading eyebrow="Try one of these" title="Popular pages" align="center" />
    <ul class="mx-auto mt-10 grid max-w-3xl gap-4 sm:grid-cols-2">
        @foreach ([
            ['Properties for Sale', route('properties'), 'Everything currently on the market'],
            ['Recently Sold', route('sold'), 'What comparable property actually fetched'],
            ['Selling & Free Appraisal', route('selling').'#appraisal', 'Find out what your place is worth'],
            ['About', route('about'), 'Who you would be dealing with'],
        ] as [$label, $url, $blurb])
            <li>
                <a href="{{ $url }}" class="card block p-5 transition hover:border-ink-300">
                    <p class="font-sans font-semibold text-ink-900">{{ $label }}</p>
                    <p class="mt-1 text-sm text-ink-500">{{ $blurb }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</section>
@endsection
