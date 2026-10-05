<?php

namespace Tests\Support;

use App\Support\Ai\AiConnector;
use App\Support\Ai\AiUnavailable;

/**
 * Stands in for the model in tests.
 *
 * The suite must never make a real API call - that would make it slow, flaky,
 * dependent on a network and, unusually for a test suite, expensive. This
 * records what it was asked so tests can assert on the prompt the app builds,
 * which is the part actually worth testing.
 */
class FakeAiConnector implements AiConnector
{
    public ?string $system = null;

    public ?string $prompt = null;

    public int $calls = 0;

    public function __construct(
        private string $reply = 'Thanks for getting in touch. I will confirm and come back to you.',
        private bool $configured = true,
        private bool $shouldFail = false,
    ) {
    }

    public static function unconfigured(): self
    {
        return new self(configured: false);
    }

    public static function failing(): self
    {
        return new self(shouldFail: true);
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function complete(string $system, string $prompt): string
    {
        $this->calls++;
        $this->system = $system;
        $this->prompt = $prompt;

        if ($this->shouldFail) {
            throw AiUnavailable::callFailed('fake outage');
        }

        return $this->reply;
    }
}
