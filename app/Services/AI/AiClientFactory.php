<?php

namespace App\Services\AI;

use App\Services\AI\Contracts\AiClient;
use App\Services\AI\Exceptions\AiException;

class AiClientFactory
{
    public function __construct(protected AiConfig $config)
    {
    }

    /**
     * @throws AiException si no hay una clave de API configurada
     */
    public function make(): AiClient
    {
        if (! $this->config->isConfigured()) {
            throw AiException::notConfigured();
        }

        return match ($this->config->provider()) {
            'google' => new GoogleGeminiClient($this->config->baseUrl(), $this->config->apiKey(), $this->config->model()),
            default => new OpenAiCompatibleClient($this->config->baseUrl(), $this->config->apiKey(), $this->config->model()),
        };
    }
}
