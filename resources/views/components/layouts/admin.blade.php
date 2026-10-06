@props(["title" => "Admin", "heading" => null, "actions" => null])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} | {{ config('agent.agency') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sand-100" @if (session('status')) data-flash="{{ session('status') }}" @endif>

@php
    $items = [
        ['route' => 'admin.dashboard',          'label' => 'Dashboard',    'match' => 'admin.dashboard'],
        ['route' => 'admin.properties.index',   'label' => 'Properties',   'match' => 'admin.properties.*'],
        ['route' => 'admin.enquiries.index',    'label' => 'Enquiries',    'match' => 'admin.enquiries.*'],
        ['route' => 'admin.assistant.index',    'label' => 'Assistant',    'match' => 'admin.assistant.*'],
        ['route' => 'admin.calls.index',        'label' => 'Calls',        'match' => 'admin.calls.*'],
        ['route' => 'admin.appointments.index', 'label' => 'Appointments', 'match' => 'admin.appointments.*'],
        ['route' => 'admin.testimonials.index', 'label' => 'Testimonials', 'match' => 'admin.testimonials.*'],
        ['route' => 'admin.settings.index',     'label' => 'Settings',     'match' => 'admin.settings.*'],
        ['route' => 'admin.profile.edit',       'label' => 'Profile',      'match' => 'admin.profile.*'],
    ];
    // Counts what still owes someone a reply, not mail merely left unopened -
    // an enquiry the agent skimmed and never answered is still outstanding work.
    $outstanding = \App\Models\Enquiry::needsReply()->count();
    $toConfirm = \App\Models\Appointment::where('status', \App\Models\Appointment::REQUESTED)->where('starts_at', '>=', now())->count();
@endphp

<div class="flex min-h-screen flex-col lg:flex-row">
    <aside class="flex shrink-0 flex-col border-b border-ink-200 bg-ink-900 text-ink-300 lg:w-60 lg:border-r lg:border-b-0">
        <div class="h-1 w-full shrink-0 bg-gradient-to-r from-teal-500 via-brass-500 to-clay-500" aria-hidden="true"></div>
        <div class="flex items-center justify-between p-5 lg:block">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ \App\Support\Media::agent('logo_mark') }}" alt="" width="34" height="34" class="h-8.5 w-8.5" aria-hidden="true">
                <span>
                    <span class="block font-display text-lg leading-tight text-white">{{ config('agent.agency') }}</span>
                    <span class="block text-[11px] tracking-[0.16em] text-ink-500 uppercase">Admin</span>
                </span>
            </a>
            <a href="{{ route('home') }}" class="text-xs text-ink-400 hover:text-white lg:hidden">View site &rarr;</a>
        </div>

        <nav class="px-3 pb-4 lg:pb-0">
            <ul class="flex gap-1 overflow-x-auto lg:block lg:space-y-1">
                @foreach ($items as $item)
                    <li>
                        <a href="{{ route($item['route']) }}"
                           @class([
                               'flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-sm font-medium whitespace-nowrap transition-colors',
                               'bg-white/10 text-white' => request()->routeIs($item['match']),
                               'text-ink-300 hover:bg-white/5 hover:text-white' => ! request()->routeIs($item['match']),
                           ])>
                            {{ $item['label'] }}
                            @if ($item['label'] === 'Enquiries' && $outstanding)
                                <span class="rounded-full bg-brass-500 px-2 py-0.5 text-[11px] font-bold text-white">{{ $outstanding }}</span>
                            @endif
                            @if ($item['label'] === 'Appointments' && $toConfirm)
                                <span class="rounded-full bg-brass-500 px-2 py-0.5 text-[11px] font-bold text-white" title="Waiting for you to confirm">{{ $toConfirm }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        <div class="mt-auto hidden p-4 lg:block">
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm text-ink-400 hover:bg-white/5 hover:text-white">View site &rarr;</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-ink-400 hover:bg-white/5 hover:text-white">Log out</button>
            </form>
            <a href="{{ route('admin.profile.edit') }}" class="mt-3 flex items-center gap-2.5 rounded-lg px-3 py-2 hover:bg-white/5">
                @php $me = \App\Support\Media::agent('avatar') ?? \App\Support\Media::agent('photo'); @endphp
                @if ($me)
                    <img src="{{ $me }}" alt="" class="h-7 w-7 shrink-0 rounded-full object-cover" aria-hidden="true">
                @endif
                <span class="min-w-0">
                    <span class="block truncate text-xs font-medium text-ink-200">{{ auth()->user()?->name }}</span>
                    <span class="block truncate text-[11px] text-ink-500">{{ auth()->user()?->email }}</span>
                </span>
            </a>
        </div>
    </aside>

    <main class="flex-1">
        <header class="border-b border-ink-200 bg-white">
            <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <h1 class="font-display text-2xl text-ink-900">{{ $heading ?? ($title ?? 'Admin') }}</h1>
                <div class="flex items-center gap-3">
                    @isset($actions){{ $actions }}@endisset
                    <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-ink-500 hover:text-ink-900">Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="px-5 py-6 sm:px-8 sm:py-8">
            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Please fix the following:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>
