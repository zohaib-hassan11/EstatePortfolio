<?php

namespace App\Support\Ai;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIStatusException;
use Throwable;

/**
 * The real connector: one call to Claude, one string back.
 *
 * Every failure - no credentials, a network problem, a rate limit, a refusal,
 * an empty body - leaves here as AiUnavailable, so callers have exactly one
 * thing to catch and the agent always falls back to writing it themselves.
 */
class ClaudeConnector implements AiConnector
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
        private readonly int $maxTokens,
        private readonly int $timeout,
        private ?Client $client = null,
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    public function complete(string $system, string $prompt): string
    {
        if (! $this->isConfigured()) {
            throw AiUnavailable::notConfigured();
        }

        try {
            // `thinking` is deliberately omitted: Claude Opus 5 runs adaptive
            // thinking by default, which is what we want, and maxTokens is the
            // real cost ceiling on a short reply.
            $message = $this->client()->messages->create(
                maxTokens: $this->maxTokens,
                messages: [['role' => 'user', 'content' => $prompt]],
                model: $this->model,
                system: $system,
                requestOptions: ['timeout' => (float) $this->timeout],
            );
        } catch (APIStatusException $e) {
            throw AiUnavailable::callFailed($e->type?->value ?? $e->getMessage());
        } catch (Throwable $e) {
            throw AiUnavailable::callFailed($e->getMessage());
        }

        // A safety decline comes back as a normal 200, so it has to be checked
        // before the content is read rather than caught as an error.
        if ($message->stopReason === 'refusal') {
            throw AiUnavailable::callFailed('the model declined this request');
        }

        foreach ($message->content as $block) {
            if (($block->type ?? null) === 'text' && filled($block->text)) {
                return $block->text;
            }
        }

        throw AiUnavailable::emptyResponse();
    }

    private function client(): Client
    {
        return $this->client ??= new Client(apiKey: $this->apiKey);
    }
}
