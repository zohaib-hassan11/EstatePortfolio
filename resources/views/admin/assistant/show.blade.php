<x-layouts.admin title="Assistant chat" heading="Assistant chat">
    <x-slot:actions>
        <a href="{{ route('admin.assistant.index') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">&larr; All chats</a>
    </x-slot:actions>

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr] lg:items-start">
        <section class="card p-6">
            <p class="text-sm text-ink-400">
                Started {{ $conversation->created_at->format('l j F Y, g:ia') }}
                &middot; {{ $conversation->visitor_messages }} {{ Str::plural('message', $conversation->visitor_messages) }} from the visitor
            </p>

            <div class="mt-6">
                @include('admin.assistant._transcript', ['messages' => $conversation->messages])
            </div>
        </section>

        <aside class="space-y-4">
            <div class="card p-6">
                <h2 class="font-sans text-base font-semibold">Outcome</h2>
                @if ($conversation->enquiry)
                    <p class="mt-3 text-sm text-ink-600">
                        <span class="font-semibold text-emerald-700">Became a lead.</span>
                        {{ $conversation->enquiry->name }} left their details.
                    </p>
                    <a href="{{ route('admin.enquiries.show', $conversation->enquiry) }}" class="btn-primary mt-4 w-full">Open the enquiry</a>
                @else
                    <p class="mt-3 text-sm text-ink-500">
                        No contact details were left. Anonymous chats are deleted after {{ config('ai.chat.retention_days') }} days.
                    </p>
                @endif
            </div>

            @if ($conversation->property)
                <div class="card p-6">
                    <h2 class="font-sans text-base font-semibold">Started on</h2>
                    <div class="mt-4 flex items-center gap-4">
                        <img src="{{ $conversation->property->heroImageUrl() }}" alt="" class="h-14 w-20 shrink-0 rounded-md object-cover">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-ink-900">{{ $conversation->property->title }}</p>
                            <a href="{{ route('admin.properties.edit', $conversation->property) }}" class="text-sm font-medium text-brass-600 hover:underline">Edit listing</a>
                        </div>
                    </div>
                </div>
            @endif

            @if ($conversation->unansweredQuestions->isNotEmpty())
                <div class="card p-6">
                    <h2 class="font-sans text-base font-semibold">Could not answer</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($conversation->unansweredQuestions as $question)
                            <li>
                                <span class="text-xs font-semibold tracking-wide text-brass-700 uppercase">{{ $question->topicLabel() }}</span>
                                <span class="block text-ink-700">{{ $question->question }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
</x-layouts.admin>
