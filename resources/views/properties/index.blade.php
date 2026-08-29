@extends('layouts.app', [
    'nav' => 'properties',
    'title' => 'Properties for Sale',
    'description' => 'Houses, flats and plots for sale with '.config('agent.name').' across DHA, Bahria Town, Gulberg and Model Town. Filter by area, price, bedrooms and property type.',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Properties for Sale', 'url' => null],
    ],
    'schema' => [\App\Support\Seo::itemList($properties->getCollection(), 'Properties for sale')],
])

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-12 lg:py-16">
        <div class="max-w-2xl">
            <p class="eyebrow">On the market</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">Properties for sale</h1>
            <p class="prose-page mt-4">
                {{ $properties->total() }} {{ Str::plural('property', $properties->total()) }} currently available.
                Not seeing it? Off-market properties come up regularly &mdash;
                <a href="{{ route('contact') }}" class="text-brass-600 underline">tell me what you're after</a>.
            </p>
        </div>
    </div>
</section>

<section class="container-page py-10">
    <x-island
        name="PropertyFilter"
        :props="[
            'action' => route('properties'),
            'suburbs' => $suburbs,
            'initial' => array_map(fn ($v) => (string) $v, array_filter($filters, fn ($v) => $v !== null)),
            'types' => config('agent.property_types'),
            'priceBands' => config('agent.price_bands'),
        ]"
    >
        {{-- No-JS fallback: the same GET form, always expanded --}}
        <form action="{{ route('properties') }}" method="GET" class="rounded-card border border-ink-100 bg-white p-4">
            <div class="flex flex-col gap-3 sm:flex-row">
                <label class="sr-only" for="fallback-q">Search</label>
                <input id="fallback-q" name="q" value="{{ $filters['q'] ?? '' }}" class="input flex-1" placeholder="Search area, block or property">
                <button type="submit" class="btn-primary shrink-0 sm:w-auto">Search</button>
            </div>
        </form>
    </x-island>

    @if ($properties->isEmpty())
        <div class="mt-10">
            <x-empty-state
                title="No properties match those filters"
                message="Try widening the price range or clearing the suburb filter. New listings are added most weeks."
                :action-url="route('properties')"
                action-label="Clear filters" />
        </div>
    @else
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($properties as $property)
                <x-property-card :property="$property" :eager="$loop->index < 3" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $properties->links() }}
        </div>
    @endif
</section>

@endsection
