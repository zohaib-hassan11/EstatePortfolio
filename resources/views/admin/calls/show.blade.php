<x-layouts.admin title="Call" :heading="'Call with '.($call->enquiry?->name ?? $call->from_number ?? 'unknown caller')">
    <x-slot:actions>
        <a href="{{ route('admin.calls.index') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">&larr; All calls</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr] lg:items-start">
        <section class="card p-6">
            <h2 class="font-sans text-base font-semibold">Summary</h2>
            <p class="mt-2 text-[15px] leading-relaxed text-ink-700">{{ $call->summary ?? 'The voice provider has not sent an analysis for this call yet.' }}</p>

            <h2 class="mt-8 font-sans text-base font-semibold">Transcript</h2>
            @if ($call->transcript)
                <div class="mt-3 max-h-[32rem] overflow-y-auto rounded-lg bg-sand-50 p-4 text-sm leading-relaxed whitespace-pre-line text-ink-700">{{ $call->transcript }}</div>
            @else
                <p class="mt-2 text-sm text-ink-400">No transcript.</p>
            @endif

            @if (filled($call->analysis))
                <h2 class="mt-8 font-sans text-base font-semibold">What the voice agent extracted</h2>
                <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                    @foreach ($call->analysis as $key => $value)
                        <div>
                            <dt class="text-xs tracking-wide text-ink-400 uppercase">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="mt-0.5 text-sm text-ink-800">{{ is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </section>

        <aside class="space-y-4">
            <div class="card p-6">
                <h2 class="font-sans text-base font-semibold">Call</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-ink-400">When</dt><dd class="text-ink-800">{{ $call->started_at?->setTimezone(config('agent.appointments.timezone'))->format('l j F Y, g:ia') ?? '—' }}</dd></div>
                    <div><dt class="text-ink-400">Duration</dt><dd class="text-ink-800 tabular-nums">{{ $call->durationLabel() }}</dd></div>
                    <div><dt class="text-ink-400">From / to</dt><dd class="text-ink-800">{{ $call->from_number ?? '—' }} &rarr; {{ $call->to_number ?? '—' }}</dd></div>
                    @if ($call->sentiment)<div><dt class="text-ink-400">Caller sentiment</dt><dd class="text-ink-800">{{ $call->sentiment }}</dd></div>@endif
                    @if ($call->disconnection_reason)<div><dt class="text-ink-400">Ended by</dt><dd class="text-ink-800">{{ str_replace('_', ' ', $call->disconnection_reason) }}</dd></div>@endif
                    @if ($call->costLabel())<div><dt class="text-ink-400">Cost</dt><dd class="text-ink-800">{{ $call->costLabel() }}</dd></div>@endif
                    <div><dt class="text-ink-400">Call ID</dt><dd class="break-all text-xs text-ink-500">{{ $call->provider_call_id }}</dd></div>
                </dl>
                @if ($call->recording_url)
                    <a href="{{ $call->recording_url }}" target="_blank" rel="noopener noreferrer" class="btn-outline mt-5 w-full">Open recording</a>
                    <p class="mt-1.5 text-xs text-ink-400">Recording links from the voice provider can expire - if this one has, open the call in the Retell dashboard.</p>
                @endif
            </div>

            @if ($call->enquiry)
                <div class="card p-6">
                    <h2 class="font-sans text-base font-semibold">Lead</h2>
                    <p class="mt-3 flex items-center gap-2 text-sm text-ink-700">
                        <x-priority-chip :priority="$call->enquiry->priority" />
                        {{ $call->enquiry->name }}
                    </p>
                    @if ($call->enquiry->qualification)
                        <p class="mt-2 text-xs text-ink-500">{{ $call->enquiry->qualification['summary'] ?? '' }}</p>
                    @endif
                    <a href="{{ route('admin.enquiries.show', $call->enquiry) }}" class="btn-primary mt-4 w-full">Open the lead</a>
                </div>
            @endif
        </aside>
    </div>
</x-layouts.admin>
