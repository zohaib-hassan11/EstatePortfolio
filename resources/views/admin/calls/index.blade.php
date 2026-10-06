<x-layouts.admin title="Calls" heading="Calls">
    <section aria-label="Last 30 days" class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Calls', number_format($stats['calls']), 'Handled by the phone agent'],
            ['Leads', number_format($stats['leads']), 'Callers now in your inbox'],
            ['Minutes', number_format($stats['minutes']), 'Total talk time'],
            ['Call cost', '$'.number_format($stats['cost'] / 100, 2), 'As reported by the voice provider'],
        ] as [$label, $value, $hint])
            <div class="card p-5">
                <p class="text-sm font-medium text-ink-500">{{ $label }}</p>
                <p class="mt-1.5 font-sans text-3xl font-semibold tracking-tight text-ink-900 tabular-nums">{{ $value }}</p>
                <p class="mt-1 text-xs text-ink-400">{{ $hint }} &middot; 30 days</p>
            </div>
        @endforeach
    </section>

    @if ($calls->isEmpty())
        <x-empty-state title="No calls yet" message="Calls handled by the phone agent appear here once n8n forwards them to this site." />
    @else
        <div class="card divide-y divide-ink-50">
            @foreach ($calls as $call)
                <a href="{{ route('admin.calls.show', $call) }}"
                   class="flex flex-col gap-2 px-5 py-4 hover:bg-sand-50 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="flex flex-wrap items-center gap-2">
                            @if ($call->enquiry)
                                <x-priority-chip :priority="$call->enquiry->priority" class="!text-[11px]" />
                            @endif
                            <span class="truncate font-semibold text-ink-900">{{ $call->enquiry?->name ?? ($call->from_number ?? 'Unknown caller') }}</span>
                            <span class="shrink-0 rounded-full bg-ink-100 px-2 py-0.5 text-[11px] font-semibold text-ink-600 uppercase">{{ $call->direction ?? 'call' }}</span>
                            @if ($call->in_voicemail)
                                <span class="shrink-0 rounded-full bg-ink-50 px-2 py-0.5 text-[11px] font-semibold text-ink-500">Voicemail</span>
                            @endif
                        </p>
                        <p class="mt-1 truncate text-sm text-ink-500">{{ $call->summary ?? 'No summary yet' }}</p>
                    </div>
                    <div class="shrink-0 text-right text-xs text-ink-400">
                        <span class="block tabular-nums">{{ $call->durationLabel() }}</span>
                        {{ $call->started_at?->setTimezone(config('agent.appointments.timezone'))->format('j M, g:ia') }}
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $calls->links() }}</div>
    @endif
</x-layouts.admin>
