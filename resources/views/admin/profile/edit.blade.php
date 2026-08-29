<x-layouts.admin title="Profile" heading="Your profile">
    <x-slot:actions>
        <a href="{{ route('admin.settings.index') }}" class="text-sm font-medium text-ink-500 hover:text-ink-900">Site settings &rarr;</a>
    </x-slot:actions>

    <div class="grid gap-6 xl:grid-cols-[1fr_1.4fr] xl:items-start">

        {{-- Portrait + summary --}}
        <div class="space-y-6">
            <section class="card border-t-4 border-t-teal-500 p-6 text-center">
                @php $portrait = \App\Support\Media::agent('avatar') ?? \App\Support\Media::agent('photo'); @endphp

                @if ($portrait)
                    <img src="{{ $portrait }}" alt="{{ $user->name }}"
                         class="mx-auto h-32 w-32 rounded-full object-cover ring-4 ring-teal-100" width="256" height="256">
                @else
                    <div class="mx-auto flex h-32 w-32 items-center justify-center rounded-full bg-ink-100 font-display text-3xl text-ink-500">
                        {{ Str::of($user->name)->explode(' ')->take(2)->map(fn ($p) => Str::substr($p, 0, 1))->implode('') }}
                    </div>
                @endif

                <h2 class="mt-4 font-display text-2xl text-ink-900">{{ $user->name }}</h2>
                <p class="text-sm text-ink-500">{{ config('agent.title') }}, {{ config('agent.agency') }}</p>

                <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" class="mt-6 text-left">
                    @csrf
                    <label class="label" for="photo">Replace portrait</label>
                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" required
                           class="w-full rounded-lg border border-dashed border-ink-300 bg-sand-50 px-3 py-3 text-sm file:mr-3 file:rounded-full file:border-0 file:bg-ink-900 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-sand-50">
                    <p class="mt-1 text-xs text-ink-400">Square works best. Used on the home page, About page and every listing.</p>
                    @error('photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="btn-primary mt-3 w-full">Upload portrait</button>
                </form>
            </section>

            <section class="card p-6">
                <h2 class="font-sans text-base font-semibold">At a glance</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    @foreach ([
                        ['Listings managed', $stats['listings'], 'text-teal-600'],
                        ['Transfers completed', $stats['sold'], 'text-brass-700'],
                        ['Enquiries received', $stats['enquiries'], 'text-indigo-600'],
                    ] as [$label, $value, $tone])
                        <div class="flex items-center justify-between gap-4 border-b border-ink-50 pb-2.5 last:border-0">
                            <dt class="text-ink-500">{{ $label }}</dt>
                            <dd class="font-sans text-lg font-semibold {{ $tone }} tabular-nums">{{ number_format($value) }}</dd>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between gap-4">
                        <dt class="text-ink-500">Account created</dt>
                        <dd class="text-ink-800">{{ $stats['joined']?->format('j M Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        {{-- Account details --}}
        <div class="space-y-6">
            <section class="card border-t-4 border-t-brass-500 p-6 sm:p-8">
                <h2 class="font-sans text-base font-semibold">Account details</h2>
                <p class="mt-1 mb-5 text-sm text-ink-500">
                    This is the account you sign in with. It is separate from the public contact details, which live in
                    <a href="{{ route('admin.settings.index', ['section' => 'contact']) }}" class="font-medium text-brass-600 hover:underline">settings</a>.
                </p>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
                    @csrf @method('PUT')

                    <x-field label="Name" name="name" required>
                        <input id="name" name="name" class="input" value="{{ old('name', $user->name) }}" required>
                    </x-field>

                    <x-field label="Sign-in email" name="email" required>
                        <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required>
                    </x-field>

                    <button type="submit" class="btn-primary">Save details</button>
                </form>
            </section>

            <section class="card border-t-4 border-t-clay-500 p-6 sm:p-8">
                <h2 class="font-sans text-base font-semibold">Change password</h2>
                <p class="mt-1 mb-5 text-sm text-ink-500">
                    Changing your password signs out every other device.
                </p>

                <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5">
                    @csrf @method('PUT')

                    <x-field label="Current password" name="current_password" required>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                               class="input sm:max-w-sm" required>
                    </x-field>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field label="New password" name="password" required hint="At least 8 characters.">
                            <input id="password" name="password" type="password" autocomplete="new-password" class="input" required>
                        </x-field>
                        <x-field label="Confirm new password" name="password_confirmation" required>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   autocomplete="new-password" class="input" required>
                        </x-field>
                    </div>

                    <button type="submit" class="btn-primary">Change password</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.admin>
