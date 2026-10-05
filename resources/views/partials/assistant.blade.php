{{--
    The chat bubble. Only rendered when an API key is set and the assistant is
    switched on - otherwise the page is exactly what it was before it existed.
    On a listing page it is scoped to that listing.
--}}
@if (app(\App\Services\Assistant\PropertyAssistant::class)->isAvailable())
    <x-island
        name="PropertyAssistant"
        :props="[
            'endpoint'      => route('assistant.show'),
            'property'      => $property?->slug,
            'propertyTitle' => $property?->title,
            'agentName'     => config('agent.name'),
            'agentPhoto'    => \App\Support\Media::agent('avatar'),
            'phone'         => config('agent.phone'),
            'phoneDial'     => config('agent.phone_dial'),
            'whatsapp'      => config('agent.whatsapp'),
            'privacyUrl'    => route('privacy'),
        ]"
    />
@endif
