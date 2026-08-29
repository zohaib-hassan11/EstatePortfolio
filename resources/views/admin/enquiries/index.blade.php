<x-layouts.admin title="Enquiries" heading="Enquiries">
    <form method="GET" class="mb-6 flex flex-wrap gap-3">
        <label class="sr-only" for="enq-type">Type</label>
        <select id="enq-type" name="type" class="input sm:w-56" onchange="this.form.submit()">
            <option value="">All enquiry types</option>
            @foreach (['property' => 'Property enquiries', 'appraisal' => 'Free appraisals', 'contact' => 'General contact'] as $value => $label)
                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <label class="flex items-center gap-2 text-sm text-ink-600">
            <input type="checkbox" name="unread" value="1" class="rounded border-ink-300 text-ink-900 focus:ring-brass-500"
                   @checked(request()->boolean('unread')) onchange="this.form.submit()">
            Unread only ({{ $unreadCount }})
        </label>

        <noscript><button type="submit" class="btn-primary">Filter</button></noscript>
    </form>

    @if ($enquiries->isEmpty())
        <x-empty-state title="No enquiries here" message="Enquiries from the website forms land in this inbox." />
    @else
        <div class="card divide-y divide-ink-50">
            @foreach ($enquiries as $enquiry)
                <a href="{{ route('admin.enquiries.show', $enquiry) }}"
                   class="flex flex-col gap-2 px-5 py-4 hover:bg-sand-50 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="flex items-center gap-2">
                            @unless ($enquiry->isRead())
                                <span class="h-2 w-2 shrink-0 rounded-full bg-brass-500" title="Unread"></span>
                            @endunless
                            <span @class(['truncate', 'font-semibold text-ink-900' => ! $enquiry->isRead(), 'text-ink-700' => $enquiry->isRead()])>
                                {{ $enquiry->name }}
                            </span>
                            <span class="shrink-0 rounded-full bg-ink-100 px-2 py-0.5 text-[11px] font-semibold text-ink-600 uppercase">
                                {{ $enquiry->typeLabel() }}
                            </span>
                        </p>
                        <p class="mt-1 truncate text-sm text-ink-500">
                            {{ $enquiry->email }}@if ($enquiry->phone) &middot; {{ $enquiry->phone }} @endif
                            @if ($enquiry->property) &middot; re: {{ $enquiry->property->title }} @endif
                        </p>
                        @if ($enquiry->message)
                            <p class="mt-1 truncate text-sm text-ink-400">{{ $enquiry->message }}</p>
                        @endif
                    </div>
                    <span class="shrink-0 text-xs text-ink-400">{{ $enquiry->created_at->format('j M Y, g:ia') }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $enquiries->links() }}</div>
    @endif
</x-layouts.admin>
