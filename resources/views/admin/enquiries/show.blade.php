<x-layouts.admin :title="'Enquiry from '.$enquiry->name" :heading="$enquiry->typeLabel()">
    <x-slot:actions>
        <a href="{{ route('admin.enquiries.index') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">&larr; All enquiries</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr] lg:items-start">
        <section class="card p-6">
            <h2 class="font-display text-2xl text-ink-900">{{ $enquiry->name }}</h2>
            <p class="mt-1 text-sm text-ink-400">Received {{ $enquiry->created_at->format('l j F Y, g:ia') }}</p>

            @if ($enquiry->message)
                <div class="mt-6 rounded-lg bg-sand-50 p-5">
                    <p class="text-[15px] leading-relaxed whitespace-pre-line text-ink-700">{{ $enquiry->message }}</p>
                </div>
            @else
                <p class="mt-6 text-sm text-ink-400">No message was included.</p>
            @endif

            @if (filled($enquiry->details))
                <dl class="mt-6 grid gap-4 border-t border-ink-100 pt-6 sm:grid-cols-2">
                    @foreach ($enquiry->details as $key => $value)
                        <div>
                            <dt class="text-xs tracking-wide text-ink-400 uppercase">{{ str_replace('_', ' ', $key) }}</dt>
                            <dd class="mt-0.5 text-[15px] text-ink-800">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            @if ($enquiry->property)
                <div class="mt-6 flex items-center gap-4 rounded-lg border border-ink-100 p-4">
                    <img src="{{ $enquiry->property->heroImageUrl() }}" alt="" class="h-14 w-20 shrink-0 rounded-md object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs tracking-wide text-ink-400 uppercase">Enquiring about</p>
                        <p class="truncate font-medium text-ink-900">{{ $enquiry->property->title }}</p>
                        <p class="text-sm text-ink-500">{{ $enquiry->property->fullAddress() }}</p>
                    </div>
                    <a href="{{ route('admin.properties.edit', $enquiry->property) }}" class="shrink-0 text-sm font-medium text-brass-600 hover:underline">Open</a>
                </div>
            @endif
        </section>

        <aside class="space-y-4">
            <div class="card p-6">
                <h2 class="font-sans text-base font-semibold">Reply</h2>
                <div class="mt-4 space-y-2">
                    <a href="mailto:{{ $enquiry->email }}?subject={{ urlencode('Re: your enquiry with '.config('agent.name')) }}" class="btn-primary w-full">
                        Email {{ Str::before($enquiry->email, '@') }}
                    </a>
                    @if ($enquiry->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $enquiry->phone) }}" class="btn-outline w-full">Call {{ $enquiry->phone }}</a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->phone) }}" target="_blank" rel="noopener noreferrer"
                           class="btn w-full bg-[#25D366] text-ink-900 hover:brightness-95">WhatsApp</a>
                    @endif
                </div>

                <dl class="mt-6 space-y-3 border-t border-ink-100 pt-5 text-sm">
                    <div><dt class="text-ink-400">Email</dt><dd class="break-all text-ink-800">{{ $enquiry->email }}</dd></div>
                    @if ($enquiry->phone)
                        <div><dt class="text-ink-400">Phone</dt><dd class="text-ink-800">{{ $enquiry->phone }}</dd></div>
                    @endif
                    <div><dt class="text-ink-400">Type</dt><dd class="text-ink-800">{{ $enquiry->typeLabel() }}</dd></div>
                </dl>
            </div>

            <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}"
                  data-confirm="This enquiry will be permanently removed."
                  data-confirm-title="Delete this enquiry?">
                @csrf @method('DELETE')
                <button type="submit" class="btn-outline w-full !border-red-200 text-red-600 hover:!bg-red-600 hover:!text-white">
                    Delete enquiry
                </button>
            </form>
        </aside>
    </div>
</x-layouts.admin>
