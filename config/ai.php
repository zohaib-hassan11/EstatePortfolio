<?php

/*
|--------------------------------------------------------------------------
| AI assistance
|--------------------------------------------------------------------------
|
| Powers the reply drafts in the enquiry inbox. Everything here is optional:
| with no API key the app binds a connector that reports itself unconfigured,
| the "Draft reply" button never appears, and the rest of the admin behaves
| exactly as it did before. AI is an enhancement layer, never a dependency of
| the core flow.
|
| Nothing written by the model is ever sent to a client automatically - drafts
| land in a box the agent edits first.
|
*/

return [
    'key' => env('ANTHROPIC_API_KEY'),

    'model' => env('AI_MODEL', 'claude-opus-5'),

    /*
     | A reply to a property question is a few short paragraphs. This is a
     | deliberate ceiling on a deliberately short output, not a guess - it also
     | caps what a single click can cost.
     */
    'max_tokens' => (int) env('AI_MAX_TOKENS', 1024),

    /* Seconds to wait before giving up and letting the agent write it himself. */
    'timeout' => (int) env('AI_TIMEOUT', 30),
];
