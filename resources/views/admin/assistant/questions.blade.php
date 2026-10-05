<x-layouts.admin title="Unanswered questions" heading="Unanswered questions">
    <x-slot:actions>
        <a href="{{ route('admin.assistant.index') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">&larr; All chats</a>
    </x-slot:actions>

    <p class="mb-6 max-w-2xl text-sm text-ink-500">
        Questions visitors asked the assistant in the last {{ $days }} days that your listings could not answer,
        so it told them you would confirm. The most-asked topics are the facts most worth adding to your listings.
    </p>

    @if ($total === 0)
        <x-empty-state title="Nothing unanswered" message="When the assistant has to defer a question to you, it shows up here." />
    @else
        <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr] lg:items-start">
            <section class="card divide-y divide-ink-50" aria-label="By topic">
                @php $max = $byTopic->max('count'); @endphp
                @foreach ($byTopic as $row)
                    <div class="px-5 py-4">
                        <div class="flex items-baseline justify-between gap-4">
                            <h2 class="font-sans text-base font-semibold text-ink-900">{{ $row['label'] }}</h2>
                            <span class="text-sm font-semibold text-ink-700 tabular-nums">{{ $row['count'] }}</span>
                        </div>
                        <div class="mt-2 h-1.5 rounded-full bg-ink-50" aria-hidden="true">
                            <div class="h-1.5 rounded-full bg-brass-500" style="width: {{ round($row['count'] / $max * 100) }}%"></div>
                        </div>
                        <ul class="mt-3 space-y-1.5 text-sm text-ink-600">
                            @foreach ($row['examples'] as $example)
                                <li>
                                    &ldquo;{{ $example->question }}&rdquo;
                                    @if ($example->property)
                                        <span class="text-ink-400">&middot; {{ $example->property->title }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </section>

            <aside class="card p-6" aria-label="By listing">
                <h2 class="font-sans text-base font-semibold">Listings with the most gaps</h2>
                @if ($byProperty->isEmpty())
                    <p class="mt-3 text-sm text-ink-500">None of these questions were about a specific listing.</p>
                @else
                    <ul class="mt-4 space-y-4">
                        @foreach ($byProperty as $row)
                            <li>
                                <div class="flex items-baseline justify-between gap-3">
                                    <a href="{{ route('admin.properties.edit', $row['property']) }}" class="truncate font-medium text-ink-900 hover:underline">{{ $row['property']->title }}</a>
                                    <span class="shrink-0 text-sm font-semibold tabular-nums text-ink-700">{{ $row['count'] }}</span>
                                </div>
                                <p class="mt-0.5 text-xs text-ink-500">
                                    @foreach ($row['topics'] as $label => $count){{ $label }} ({{ $count }})@if (! $loop->last), @endif @endforeach
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </aside>
        </div>
    @endif
</x-layouts.admin>
