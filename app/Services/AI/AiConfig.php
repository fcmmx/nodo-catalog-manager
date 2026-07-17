<?php

namespace App\Services\AI;

use App\Models\Setting;

class AiConfig
{
    public function enabled(): bool
    {
        return Setting::get('ai_enabled', '0') === '1';
    }

    public function provider(): string
    {
        return Setting::get('ai_provider', 'openai');
    }

    public function model(): string
    {
        return Setting::get('ai_model', 'gpt-4o-mini');
    }

    public function baseUrl(): string
    {
        return rtrim(Setting::get('ai_base_url', 'https://api.openai.com/v1'), '/');
    }

    public function apiKey(): ?string
    {
        return Setting::get('ai_api_key');
    }

    /**
     * La configuración está lista para usarse solo si el proveedor está
     * habilitado y existe una clave de API real guardada (nunca se inventa
     * ni se usa una clave de prueba).
     */
    public function isConfigured(): bool
    {
        return $this->enabled() && ! empty($this->apiKey());
    }

    /**
     * Muestra los últimos 4 caracteres de la clave únicamente, para que el
     * usuario pueda confirmar cuál está guardada sin exponerla completa.
     */
    public function maskedApiKey(): ?string
    {
        $key = $this->apiKey();

        if (! $key) {
            return null;
        }

        return str_repeat('•', max(0, strlen($key) - 4)).substr($key, -4);
    }
}
