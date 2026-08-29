@extends('layouts.app', [
    'nav' => 'testimonials',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Testimonials', 'url' => null],
    ],
    'title' => 'Testimonials',
    'description' => 'Reviews from buyers and sellers who have worked with '.config('agent.name').' across DHA, Bahria Town, Gulberg and Model Town in Lahore.',
])

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page py-14 lg:py-20">
        <div class="max-w-3xl">
            <p class="eyebrow">Testimonials</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">What it is actually like to work with me.</h1>
            <p class="prose-page mt-6">
Every review below is from a completed transaction. If you would like to speak to a past
                client directly before you list with me, just ask &mdash; I will put you in touch.
            </p>
        </div>

        @if ($testimonials->isNotEmpty())
            <div class="mt-10 flex items-center gap-4">
                <div class="flex gap-1" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)
                        <svg class="h-6 w-6 text-brass-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-sm text-ink-500">
                    <span class="font-semibold text-ink-900">{{ number_format($testimonials->avg('rating'), 1) }} out of 5</span>
                    from {{ $testimonials->count() }} reviews
                </p>
            </div>
        @endif
    </div>
</section>

<section class="container-page py-20">
    @if ($testimonials->isEmpty())
        <x-empty-state title="No testimonials yet" message="Reviews will appear here as they come in." :action-url="route('contact')" action-label="Get in touch" />
    @else
        <div class="columns-1 gap-6 md:columns-2 lg:columns-3">
            @foreach ($testimonials as $testimonial)
                <blockquote class="card mb-6 break-inside-avoid p-7">
                    <div class="flex gap-1" aria-label="{{ $testimonial->rating }} out of 5 stars">
                        @for ($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="h-4 w-4 text-brass-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="mt-4 text-[15px] leading-relaxed text-ink-600">&ldquo;{{ $testimonial->body }}&rdquo;</p>
                    <footer class="mt-5 border-t border-ink-100 pt-4 text-sm">
                        <span class="font-semibold text-ink-900">{{ $testimonial->author }}</span>
                        @if ($testimonial->role || $testimonial->location)
                            <span class="block text-ink-400">{{ collect([$testimonial->role, $testimonial->location])->filter()->implode(' · ') }}</span>
                        @endif
                    </footer>
                </blockquote>
            @endforeach
        </div>
    @endif
</section>

<section class="container-page pb-20">
    <div class="rounded-card bg-gradient-to-br from-ink-900 via-ink-800 to-teal-900 p-8 text-center text-sand-100 sm:p-12">
        <h2 class="text-3xl text-white sm:text-4xl">Ready to talk about your place?</h2>
        <p class="mx-auto mt-4 max-w-xl text-ink-300">Start with a free appraisal. It costs nothing and there is no obligation to list.</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('selling') }}#appraisal" class="btn-accent">Book a free appraisal</a>
            <a href="tel:{{ config('agent.phone_dial') }}" class="btn-ghost-light" data-analytics="click-to-call-testimonials">Call {{ config('agent.phone') }}</a>
        </div>
    </div>
</section>

@endsection
