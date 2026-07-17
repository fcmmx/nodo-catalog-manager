<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AiClient;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Support\Facades\Http;

/**
 * Cliente compatible con la API de "chat completions" de OpenAI y con
 * cualquier proveedor "otro compatible" que exponga la misma interfaz
 * (Azure OpenAI, Groq, Together AI, servidores de modelos locales, etc.),
 * simplemente cambiando ai_base_url en Configuración → IA.
 */
class OpenAiCompatibleClient implements AiClient
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey,
        protected string $model,
    ) {
    }

    public function complete(string $systemPrompt, string $userPrompt): AiCompletionResult
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
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
        $content = $data['choices'][0]['message']['content'] ?? null;

        if ($content === null) {
            throw AiException::unknown('El proveedor respondió sin contenido utilizable.');
        }

        return new AiCompletionResult(
            content: trim($content),
            inputTokens: $data['usage']['prompt_tokens'] ?? null,
            outputTokens: $data['usage']['completion_tokens'] ?? null,
        );
    }

    protected function extractErrorMessage($response): string
    {
        $data = $response->json();

        return $data['error']['message'] ?? $response->body();
    }
}
