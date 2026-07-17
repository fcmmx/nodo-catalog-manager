<?php

namespace App\Services\AI;

class AiCompletionResult
{
    public function __construct(
        public readonly string $content,
        public readonly ?int $inputTokens = null,
        public readonly ?int $outputTokens = null,
    ) {
    }
}
