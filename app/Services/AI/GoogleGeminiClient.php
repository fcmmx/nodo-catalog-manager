<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AiClient;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Support\Facades\Http;

class GoogleGeminiClient implements AiClient
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey,
        protected string $model,
    ) {
    }

    public function complete(string $systemPrompt, string $userPrompt): AiCompletionResult
    {
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(60)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $userPrompt]]],
                ],
                'generationConfig' => ['temperature' => 0.7],
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw AiException::networkError($e->getMessage());
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw AiException::invalidToken($this->extractErrorMessage($response));
        }

        if ($response->status() === 429) {
            $message = $this->extractErrorMessage($response);
            throw str_contains(strtolower($message), 'quota')
                ? AiException::quotaExceeded($message)
                : AiException::rateLimited($message);
        }

        if ($response->failed()) {
            throw AiException::unknown("HTTP {$response->status()} — ".$this->extractErrorMessage($response));
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($content === null) {
            throw AiException::unknown('El proveedor respondió sin contenido utilizable.');
        }

        return new AiCompletionResult(
            content: trim($content),
            inputTokens: $data['usageMetadata']['promptTokenCount'] ?? null,
            outputTokens: $data['usageMetadata']['candidatesTokenCount'] ?? null,
        );
    }

    protected function extractErrorMessage($response): string
    {
        $data = $response->json();

        return $data['error']['message'] ?? $response->body();
    }
}
