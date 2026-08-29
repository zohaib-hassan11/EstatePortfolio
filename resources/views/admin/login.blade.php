<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login | {{ config('agent.agency') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-ink-900 px-5">
    <div class="w-full max-w-sm">
        <div class="text-center">
            <img src="{{ \App\Support\Media::agent('logo_light') }}" alt="{{ config('agent.agency') }}"
                 width="260" height="62" class="mx-auto h-16 w-auto">
            <p class="mt-1 text-sm text-ink-400">Sign in to manage listings and enquiries</p>
        </div>

        @php $demo = config('agent.demo'); @endphp

        @if ($demo['enabled'])
            <p class="mt-6 flex items-start gap-2.5 rounded-lg border border-brass-300 bg-brass-50 px-4 py-3 text-sm text-brass-700">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5M12 7.5h.01"/>
                </svg>
                <span>
                    <strong class="font-semibold">Demo site.</strong>
                    The credentials below are filled in already &mdash; just press Sign in.
                </span>
            </p>
        @endif

        <form method="POST" action="{{ route('admin.login.attempt') }}" class="mt-8 space-y-4 rounded-card bg-sand-50 p-7">
            @csrf

            @if ($errors->any())
                <p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</p>
            @endif

            <div>
                <label class="label" for="email">Email</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $demo['enabled'] ? $demo['email'] : '') }}"
                       class="input" autocomplete="username" required autofocus>
            </div>

            <div>
                <label class="label" for="password">Password</label>
                <input id="password" name="password" type="password"
                       value="{{ $demo['enabled'] ? $demo['password'] : '' }}"
                       class="input" autocomplete="current-password" required>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-ink-300 text-ink-900 focus:ring-brass-500">
                Keep me signed in
            </label>

            <button type="submit" class="btn-primary w-full">Sign in</button>
        </form>

        <p class="mt-6 text-center text-sm text-ink-500">
            <a href="{{ route('home') }}" class="hover:text-white">&larr; Back to the website</a>
        </p>
    </div>
</body>
</html>
