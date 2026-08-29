<x-layouts.admin title="Testimonials" heading="Testimonials">
    <x-slot:actions>
        <a href="{{ route('admin.testimonials.create') }}" class="btn-primary !px-4 !py-2 text-xs">+ New testimonial</a>
    </x-slot:actions>

    @if ($testimonials->isEmpty())
        <x-empty-state title="No testimonials yet" message="Add reviews from past clients to build trust on the site."
                       :action-url="route('admin.testimonials.create')" action-label="Add a testimonial" />
    @else
        <div class="card divide-y divide-ink-50">
            @foreach ($testimonials as $testimonial)
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="flex items-center gap-2">
                            <span class="font-medium text-ink-900">{{ $testimonial->author }}</span>
                            <span class="text-sm text-brass-500">{{ str_repeat('★', $testimonial->rating) }}</span>
                            @if ($testimonial->is_featured)
                                <span class="rounded-full bg-brass-100 px-2 py-0.5 text-[11px] font-bold text-brass-700 uppercase">Featured</span>
                            @endif
                            @unless ($testimonial->is_published)
                                <span class="rounded-full bg-ink-100 px-2 py-0.5 text-[11px] font-bold text-ink-500 uppercase">Draft</span>
                            @endunless
                        </p>
                        <p class="mt-1 line-clamp-2 text-sm text-ink-500">{{ $testimonial->body }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3 text-sm">
                        <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="font-medium text-brass-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}"
                              data-confirm="This testimonial will be permanently removed."
                              data-confirm-title="Delete this testimonial?">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $testimonials->links() }}</div>
    @endif
</x-layouts.admin>
