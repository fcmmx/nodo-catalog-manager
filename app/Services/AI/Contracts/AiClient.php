<?php

namespace App\Services\AI\Contracts;

use App\Services\AI\AiCompletionResult;

interface AiClient
{
    /**
     * @throws \App\Services\AI\Exceptions\AiException
     */
    public function complete(string $systemPrompt, string $userPrompt): AiCompletionResult;
}
