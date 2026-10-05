<?php

namespace App\Support\Ai;

/**
 * The one way this application talks to a language model.
 *
 * Everything above this line - services, controllers, views - knows only this
 * interface, so the vendor behind it can change, be switched off, or be faked
 * in tests without a single caller changing. Tests bind a fake implementation,
 * which is what keeps the suite offline, free and deterministic.
 */
interface AiConnector
{
    /**
     * Whether a real model is reachable. False means no credentials are set -
     * callers must degrade gracefully rather than fail.
     */
    public function isConfigured(): bool;

    /**
     * Run one prompt and return the model's text.
     *
     * @throws AiUnavailable when the model cannot be reached or returns nothing
     */
    public function complete(string $system, string $prompt): string;
}
