<x-layouts.admin title="Appointments" heading="Appointments">
    <div class="mb-4 flex flex-wrap items-center gap-2">
        @foreach (['' => 'Upcoming', 'past' => 'Past'] as $value => $label)
            <a href="{{ request()->fullUrlWithQuery(['view' => $value ?: null, 'page' => null]) }}"
               @class([
                   'rounded-full border px-3.5 py-1.5 text-sm font-medium transition',
                   'border-ink-900 bg-ink-900 text-sand-100' => (string) request('view') === (string) $value,
                   'border-ink-200 text-ink-600 hover:border-ink-400' => (string) request('view') !== (string) $value,
               ])>{{ $label }}</a>
        @endforeach
        @if ($toConfirm)
            <span class="ml-2 text-sm text-brass-700">{{ $toConfirm }} {{ Str::plural('viewing', $toConfirm) }} waiting for you to confirm</span>
        @endif
    </div>

    @if ($appointments->isEmpty())
        <x-empty-state :title="$past ? 'No past viewings' : 'No upcoming viewings'" message="Viewings booked by the phone agent appear here." />
    @else
        <div class="card divide-y divide-ink-50">
            @foreach ($appointments as $appointment)
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-ink-900">{{ $appointment->whenLabel() }}</p>
                        <p class="mt-0.5 truncate text-sm text-ink-600">
                            <a href="{{ route('admin.enquiries.show', $appointment->enquiry) }}" class="font-medium hover:underline">{{ $appointment->enquiry->name }}</a>
                            @if ($appointment->enquiry->phone) &middot; {{ $appointment->enquiry->phone }} @endif
                            @if ($appointment->property) &middot; {{ $appointment->property->title }} @endif
                        </p>
                        @if ($appointment->notes)
                            <p class="mt-0.5 truncate text-xs text-ink-400">{{ $appointment->notes }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="flex shrink-0 items-center gap-2">
                        @csrf @method('PATCH')
                        <label class="sr-only" for="status-{{ $appointment->id }}">Status</label>
                        <select id="status-{{ $appointment->id }}" name="status" class="input !py-1.5 text-sm" onchange="this.form.submit()">
                            @foreach (\App\Models\Appointment::statuses() as $value => $label)
                                <option value="{{ $value }}" @selected($appointment->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="btn-outline !py-1.5 text-sm">Save</button></noscript>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $appointments->links() }}</div>
    @endif
</x-layouts.admin>
