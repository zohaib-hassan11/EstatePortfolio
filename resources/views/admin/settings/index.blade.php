@php
    $tones = [
        'business' => 'teal', 'contact' => 'brass', 'office' => 'indigo',
        'social' => 'clay', 'branding' => 'teal', 'seo' => 'brass', 'format' => 'indigo',
    ];
@endphp

<x-layouts.admin title="Settings" heading="Settings">
    <x-slot:actions>
        <a href="{{ route('home') }}" target="_blank" class="text-sm font-medium text-ink-500 hover:text-ink-900">Preview site &rarr;</a>
    </x-slot:actions>

    <p class="mb-6 max-w-2xl text-sm text-ink-500">
        These override the defaults in <code class="rounded bg-ink-50 px-1.5 py-0.5 text-xs">config/agent.php</code>
        and take effect across the whole site immediately. Reset a section to fall back to the file.
    </p>

    <div class="grid gap-6 lg:grid-cols-[13rem_1fr] lg:items-start">
        {{-- Section nav --}}
        <nav aria-label="Settings sections" class="card overflow-hidden p-1.5">
            <ul class="flex gap-1 overflow-x-auto lg:block lg:space-y-0.5">
                @foreach ($sections as $key => $label)
                    <li>
                        <a href="{{ route('admin.settings.index', ['section' => $key]) }}"
                           @class([
                               'block rounded-lg px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors',
                               'bg-ink-900 text-sand-50' => $section === $key,
                               'text-ink-600 hover:bg-sand-100' => $section !== $key,
                           ])
                           @if ($section === $key) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="card border-t-4 p-6 border-t-{{ $tones[$section] }}-500 sm:p-8">
            @include('admin.settings.sections.'.$section)

            <div class="mt-8 flex flex-wrap items-center gap-4 border-t border-ink-100 pt-6">
                <button type="submit" form="settings-form" class="btn-primary">Save changes</button>

                <form method="POST" action="{{ route('admin.settings.reset', $section) }}"
                      data-confirm="Your saved values for this section will be discarded and the file defaults restored."
                      data-confirm-title="Reset this section?"
                      data-confirm-button="Yes, reset it">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-ink-500 underline hover:text-ink-900">
                        Reset to file defaults
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
