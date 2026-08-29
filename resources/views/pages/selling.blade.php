@extends('layouts.app', [
    'nav' => 'selling',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Selling & Free Appraisal', 'url' => null],
    ],
    'schema' => [\App\Support\Seo::faq([
        ['Is the appraisal really free?', 'Yes. You get a written appraisal whether or not you list with '.config('agent.name').'.'],
        ['What is the commission?', 'The customary rate in Lahore is one percent from each side, confirmed in writing before you commit.'],
        ['Do I have to give an exclusive listing?', 'No. No exclusivity is required and nothing binding is signed.'],
        ['How long will it take?', 'The average across recent transfers is '.config('agent.avg_days_on_market').' days from listing to agreed sale.'],
    ])],
    'title' => 'Selling & Free Appraisal',
    'description' => 'Book a free, no-obligation property appraisal with '.config('agent.name').' in Lahore. Pricing from recent transfers, professional photographs and weekly updates.',
])

@php $agent = config('agent'); @endphp

@section('content')

<section class="hero-wash border-b border-brass-200/60">
    <div class="container-page grid gap-12 py-14 lg:grid-cols-[1.1fr_1fr] lg:items-start lg:py-20">
        <div>
            <p class="eyebrow">Selling</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">Find out what your home is worth. Free, and genuinely no obligation.</h1>
            <p class="prose-page mt-6">
                An appraisal should be research, not a sales pitch. I'll visit the property, pull every
                recent transfer in your block and the surrounding ones, and give you a written range with
                the reasoning attached &mdash; including the transfers that argue against the top of it.
            </p>

            <ul class="mt-8 space-y-3">
                @foreach ([
                    'A written price range, backed by recent transfers in your block',
                    'An honest list of what to fix and what to leave alone',
                    'A full cost breakdown - commission, taxes and transfer fees - before you commit',
                    'No exclusivity demanded, no pressure, no follow-up you did not ask for',
                ] as $item)
                    <li class="flex gap-3 text-[15px] text-ink-600">
                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-teal-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                        </svg>
                        {{ $item }}
                    </li>
                @endforeach
            </ul>
        </div>

        <div id="appraisal" class="scroll-mt-24 rounded-card border border-ink-100 bg-white p-6 shadow-sm sm:p-8">
            @if (session('status'))
                <p class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
            @endif

            <x-island
                name="EnquiryForm"
                :props="[
                    'endpoint' => route('enquiries.store'),
                    'type' => 'appraisal',
            'types' => config('agent.property_types'),
                    'heading' => 'Request your free appraisal',
                    'subheading' => 'I reply to every request within one business day.',
                    'submitLabel' => 'Request my free appraisal',
                    'compact' => true,
                ]"
            >
                {{-- No-JS fallback: a plain POST form that hits the same endpoint --}}
                <h2 class="text-3xl">Request your free appraisal</h2>
                <p class="mt-2 text-ink-500">I reply to every request within one business day.</p>
                <noscript>
                    <form method="POST" action="{{ route('enquiries.store') }}" class="mt-6 space-y-4">
                        @csrf
                        <input type="hidden" name="type" value="appraisal">
                        <div><label class="label" for="ns-name">Your name</label><input id="ns-name" class="input" name="name" required></div>
                        <div><label class="label" for="ns-email">Email</label><input id="ns-email" class="input" type="email" name="email" required></div>
                        <div><label class="label" for="ns-phone">Phone</label><input id="ns-phone" class="input" type="tel" name="phone"></div>
                        <div><label class="label" for="ns-address">Property address</label><input id="ns-address" class="input" name="address"></div>
                        <div><label class="label" for="ns-message">Message</label><textarea id="ns-message" class="input" name="message" rows="4"></textarea></div>
                        <button type="submit" class="btn-accent w-full">Request my free appraisal</button>
                    </form>
                </noscript>
            </x-island>
        </div>
    </div>
</section>

<section class="container-page py-20">
    <x-section-heading eyebrow="The campaign" title="What the six weeks actually look like" />

    <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['Week 0', 'Appraisal and paperwork', 'We agree a price range, and I check your own file first - dues, NOC, transfer letter - so nothing surprises us at the end.'],
            ['Week 1', 'Prepare and photograph', 'A clean-up and minor fixes if they are worth it, then professional photographs and a video walkthrough.'],
            ['Weeks 2-4', 'List and show', 'Zameen, Graana, my own buyer list and the dealer network in your society. Written feedback after every viewing.'],
            ['Weeks 4-6', 'Negotiate and transfer', 'Offers brought to you in full, negotiation handled by me, then a clean run through token, dues clearance and transfer.'],
        ] as [$phase, $heading, $body])
            @php $phaseTone = ['teal', 'brass', 'clay', 'indigo'][$loop->index]; @endphp
            <li class="card border-t-4 p-6 border-t-{{ $phaseTone }}-500">
                <p class="eyebrow text-{{ $phaseTone }}-600">{{ $phase }}</p>
                <h3 class="mt-3 font-sans text-lg font-semibold">{{ $heading }}</h3>
                <p class="mt-2 text-[15px] leading-relaxed text-ink-500">{{ $body }}</p>
            </li>
        @endforeach
    </ol>
</section>

<section class="band-cream border-y border-brass-200/70">
    <div class="container-page py-20">
        <x-section-heading eyebrow="Seller FAQs" title="The questions worth asking any agent" />

        <div class="mt-10 max-w-3xl divide-y divide-ink-200">
            @foreach ([
                ['Is the appraisal really free?', 'Yes. You get a written appraisal whether or not you list with me, and whether or not you sell this year. It costs me an hour and a bit of research.'],
                ['What is your commission?', 'The customary rate in Lahore is one percent from each side. You will have that in writing, along with the transfer fee and tax estimates, before you commit to anything.'],
                ['Should I renovate before selling?', 'Usually not much. Paint, a garden tidy and clearing clutter almost always return more than they cost. A new kitchen almost never does. I will give you a short list, not a wish list.'],
                ['Do I have to give you an exclusive listing?', 'No. I do not ask for exclusivity and I do not ask you to sign anything binding. If another dealer brings the right buyer first, that is a good outcome for you.'],
                ['How long will it take?', 'My average across recent transfers is '.config('agent.avg_days_on_market').' days from listing to agreed sale. Pricing moves that number more than anything else does.'],
            ] as [$question, $answer])
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-sans text-lg font-semibold text-ink-900">
                        {{ $question }}
                        <svg class="h-5 w-5 shrink-0 text-ink-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                        </svg>
                    </summary>
                    <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-ink-500">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>

@if ($recentlySold->isNotEmpty())
    <section class="container-page py-20">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <x-section-heading eyebrow="Proof" title="Recent results" />
            <a href="{{ route('sold') }}" class="btn-outline shrink-0">All recent sales</a>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($recentlySold as $property)
                <x-property-card :property="$property" />
            @endforeach
        </div>
    </section>
@endif

@endsection
