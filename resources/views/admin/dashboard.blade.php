<x-layouts.admin title="Dashboard" heading="Dashboard">
    <x-slot:actions>
        <a href="{{ route('admin.properties.create') }}" class="btn-primary !px-4 !py-2 text-xs">+ New property</a>
    </x-slot:actions>

    {{-- Headline figures. These are stat tiles, not charts: one number each. --}}
    @php
        $tileTones = [
            ['accent' => 'bg-teal-500',   'text' => 'text-teal-600',   'wash' => 'from-teal-50'],
            ['accent' => 'bg-brass-500',  'text' => 'text-brass-700',  'wash' => 'from-brass-50'],
            ['accent' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'wash' => 'from-indigo-50'],
            ['accent' => 'bg-clay-500',   'text' => 'text-clay-600',   'wash' => 'from-clay-50'],
        ];
    @endphp

    <section aria-label="Key figures" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($tiles as $i => $tile)
            @php $tone = $tileTones[$i % count($tileTones)]; @endphp
            <div class="card relative overflow-hidden bg-gradient-to-b {{ $tone['wash'] }} to-white p-5">
                <span class="absolute inset-x-0 top-0 h-1 {{ $tone['accent'] }}" aria-hidden="true"></span>
                <p class="text-sm font-medium {{ $tone['text'] }}">{{ $tile['label'] }}</p>
                <p class="mt-1.5 font-sans text-3xl font-semibold tracking-tight text-ink-900 tabular-nums">
                    {{ $tile['value'] }}
                </p>
                <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                    @if ($tile['delta'])
                        <span @class([
                            'inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-semibold',
                            'bg-emerald-50 text-emerald-800'  => $tile['delta']['direction'] === 'up',
                            'bg-red-50 text-red-800'          => $tile['delta']['direction'] === 'down',
                            'bg-ink-50 text-ink-600'          => $tile['delta']['direction'] === 'flat',
                        ])>
                            {{-- icon + label, never colour alone --}}
                            @if ($tile['delta']['direction'] === 'up')
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 2 10 8H2z"/></svg>
                            @elseif ($tile['delta']['direction'] === 'down')
                                <svg class="h-3 w-3" viewBox="0 0 12 12" fill="currentColor" aria-hidden="true"><path d="M6 10 2 4h8z"/></svg>
                            @endif
                            {{ $tile['delta']['value'] }}
                        </span>
                        <span class="text-ink-400">{{ $tile['delta']['caption'] }}</span>
                    @else
                        <span class="text-ink-400">{{ $tile['meta'] }}</span>
                    @endif
                </div>
                @if ($tile['delta'])
                    <p class="mt-1 text-xs text-ink-400">{{ $tile['meta'] }}</p>
                @endif
            </div>
        @endforeach
    </section>

    {{-- Pipeline strip --}}
    @php
        $stageDots = ['bg-teal-500', 'bg-brass-500', 'bg-ink-700', 'bg-ink-300'];
    @endphp

    <section aria-label="Pipeline" class="card mt-4 flex flex-wrap divide-ink-100 sm:divide-x">
        @foreach ($pipeline as $i => $stage)
            <div class="min-w-[9rem] flex-1 px-5 py-4">
                <p class="flex items-center gap-1.5 text-xs tracking-wide text-ink-400 uppercase">
                    <span class="h-2 w-2 rounded-full {{ $stageDots[$i] }}" aria-hidden="true"></span>
                    {{ $stage['label'] }}
                </p>
                <p class="mt-0.5 font-sans text-xl font-semibold text-ink-900 tabular-nums">{{ $stage['value'] }}</p>
            </div>
        @endforeach
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Enquiries over time --}}
        <section class="card p-6 xl:col-span-2">
            <div class="mb-1 flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="font-sans text-base font-semibold">Enquiries per week</h2>
                <a href="{{ route('admin.enquiries.index') }}" class="text-sm font-medium text-brass-600 hover:underline">Open inbox</a>
            </div>
            <p class="mb-5 text-sm text-ink-500">Last {{ \App\Support\DashboardMetrics::WEEKS }} weeks, split by what people asked about.</p>

            <x-island
                name="StackedBarChart"
                :props="['series' => $enquiriesByWeek['series'], 'points' => $enquiriesByWeek['points'], 'height' => 250]"
            >
                {{-- Server-rendered fallback: the numbers, before Vue mounts --}}
                <p class="py-10 text-center text-sm text-ink-400">
                    {{ collect($enquiriesByWeek['points'])->sum(fn ($p) => array_sum($p['values'])) }} enquiries
                    over {{ count($enquiriesByWeek['points']) }} weeks.
                </p>
            </x-island>
        </section>

        {{-- Where the interest is --}}
        <section class="card p-6">
            <h2 class="font-sans text-base font-semibold">Most enquired listings</h2>
            <p class="mt-1 mb-5 text-sm text-ink-500">Enquiries received in the last 90 days.</p>

            <x-island name="HBarChart" :props="['rows' => $topListings, 'slot' => 1,
                'emptyMessage' => 'No enquiries against a listing yet.']">
                <ul class="space-y-2 text-sm text-ink-600">
                    @foreach ($topListings as $row)
                        <li class="flex justify-between gap-3"><span class="truncate">{{ $row['label'] }}</span><span>{{ $row['value'] }}</span></li>
                    @endforeach
                </ul>
            </x-island>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Value on the books --}}
        <section class="card p-6">
            <h2 class="font-sans text-base font-semibold">Portfolio value by area</h2>
            <p class="mt-1 mb-5 text-sm text-ink-500">Asking prices of listings currently on the market.</p>

            <x-island name="HBarChart" :props="['rows' => $valueByArea, 'slot' => 3,
                'emptyMessage' => 'No priced listings on the market.']">
                <ul class="space-y-2 text-sm text-ink-600">
                    @foreach ($valueByArea as $row)
                        <li class="flex justify-between gap-3"><span>{{ $row['label'] }}</span><span>{{ $row['formatted'] }}</span></li>
                    @endforeach
                </ul>
            </x-island>
        </section>

        {{-- Inbox --}}
        <section class="card xl:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4">
                <h2 class="font-sans text-base font-semibold">Latest enquiries</h2>
                <a href="{{ route('admin.enquiries.index') }}" class="text-sm font-medium text-brass-600 hover:underline">View all</a>
            </div>

            @forelse ($recentEnquiries as $enquiry)
                <a href="{{ route('admin.enquiries.show', $enquiry) }}"
                   class="flex items-start justify-between gap-4 border-b border-ink-50 px-6 py-3.5 last:border-0 hover:bg-sand-50">
                    <div class="min-w-0">
                        <p class="flex items-center gap-2 font-medium text-ink-900">
                            @unless ($enquiry->isRead())
                                <span class="h-2 w-2 shrink-0 rounded-full bg-brass-500" title="Unread"></span>
                            @endunless
                            {{ $enquiry->name }}
                            <span @class([
                                'rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase',
                                'bg-brass-100 text-brass-700'   => $enquiry->type === 'property',
                                'bg-teal-100 text-teal-700'     => $enquiry->type === 'appraisal',
                                'bg-indigo-100 text-indigo-700' => $enquiry->type === 'contact',
                            ])>{{ $enquiry->typeLabel() }}</span>
                        </p>
                        <p class="mt-0.5 truncate text-sm text-ink-500">
                            {{ $enquiry->property?->title ?? $enquiry->message ?? $enquiry->email }}
                        </p>
                    </div>
                    <span class="shrink-0 text-xs text-ink-400">{{ $enquiry->created_at->diffForHumans(short: true) }}</span>
                </a>
            @empty
                <p class="px-6 py-10 text-center text-sm text-ink-400">No enquiries yet.</p>
            @endforelse
        </section>
    </div>

    {{-- Recent listings --}}
    <section class="card mt-6">
        <div class="flex items-center justify-between border-b border-ink-100 px-6 py-4">
            <h2 class="font-sans text-base font-semibold">Recently added listings</h2>
            <a href="{{ route('admin.properties.index') }}" class="text-sm font-medium text-brass-600 hover:underline">Manage listings</a>
        </div>

        <div class="grid gap-px bg-ink-50 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($latestProperties as $property)
                <a href="{{ route('admin.properties.edit', $property) }}" class="group bg-white p-4 hover:bg-sand-50">
                    <img src="{{ $property->heroImageUrl() }}" alt="" class="aspect-4/3 w-full rounded-md object-cover">
                    <p class="mt-3 truncate text-sm font-medium text-ink-900">{{ $property->title }}</p>
                    <p class="mt-0.5 text-sm text-ink-500">{{ $property->priceDisplay() }}</p>
                    <x-status-chip :status="$property->status" class="mt-2 !text-[11px]" />
                </a>
            @empty
                <p class="bg-white px-6 py-10 text-center text-sm text-ink-400 sm:col-span-2 xl:col-span-4">No properties yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.admin>
