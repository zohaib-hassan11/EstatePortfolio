@extends('layouts.app', [
    'nav' => 'sold',
    'title' => 'Recently Sold',
    'description' => 'Recent transfers handled by '.config('agent.name').'. See what property across DHA, Bahria Town and Gulberg actually sold for, and how long it took.',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Recently Sold', 'url' => null],
    ],
    'schema' => [\App\Support\Seo::itemList($properties->getCollection(), 'Recently sold')],
])

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-12 lg:py-16">
        <div class="max-w-2xl">
            <p class="eyebrow">Results</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">Recently sold</h1>
            <p class="prose-page mt-4">
The best guide to what a dealer will get for your property is what they got for
                somebody else's. Here is every recent transfer, with the price and the time it took.
            </p>
        </div>

        <dl class="mt-10 grid grid-cols-1 gap-8 border-t border-ink-200 pt-8 sm:grid-cols-3">
            <x-stat :value="number_format($stats['count'])" label="Properties sold" />
            <x-stat :value="$stats['avgDays'].' days'" label="Average time on market" />
            <x-stat :value="'$'.number_format($stats['totalValue'] / 1000000, 1).'M'" label="Total value sold" />
        </dl>
    </div>
</section>

<section class="container-page py-10">
    <x-island
        name="PropertyFilter"
        :props="[
            'action' => route('sold'),
            'suburbs' => $suburbs,
            'initial' => array_map(fn ($v) => (string) $v, array_filter($filters, fn ($v) => $v !== null)),
            'types' => config('agent.property_types'),
            'priceBands' => config('agent.price_bands'),
            'showPrice' => false,
            'showSort' => false,
        ]"
    >
        <form action="{{ route('sold') }}" method="GET" class="rounded-card border border-ink-100 bg-white p-4">
            <div class="flex flex-col gap-3 sm:flex-row">
                <label class="sr-only" for="sold-q">Search</label>
                <input id="sold-q" name="q" value="{{ $filters['q'] ?? '' }}" class="input flex-1" placeholder="Search area or block">
                <button type="submit" class="btn-primary shrink-0 sm:w-auto">Search</button>
            </div>
        </form>
    </x-island>

    @if ($properties->isEmpty())
        <div class="mt-10">
            <x-empty-state title="No sales match those filters" :action-url="route('sold')" action-label="Clear filters" />
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($properties as $property)
                <x-property-card :property="$property" :eager="$loop->index < 3" />
            @endforeach
        </div>

        <div class="mt-12">{{ $properties->links() }}</div>
    @endif
</section>

<section class="container-page pb-20">
    <div class="rounded-card bg-gradient-to-br from-ink-900 via-ink-800 to-teal-900 p-8 text-center text-sand-100 sm:p-12">
        <h2 class="text-3xl text-white sm:text-4xl">Want a number for your place?</h2>
        <p class="mx-auto mt-4 max-w-xl text-ink-300">A free appraisal takes about an hour and comes with the comparable sales attached.</p>
        <a href="{{ route('selling') }}#appraisal" class="btn-accent mt-8">Book a free appraisal</a>
    </div>
</section>

@endsection
