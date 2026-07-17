<?php

namespace App\Services\Images;

use App\Services\AI\AiConfig;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Generación de fondos de imagen mediante IA. Reutiliza la misma clave de
 * API configurada para el módulo de texto (Configuración → IA). Por ahora
 * solo se soporta el endpoint de imágenes de OpenAI; si el proveedor
 * configurado es otro, se informa claramente en vez de simular un resultado.
 */
class AiImageService
{
    public function __construct(protected AiConfig $config)
    {
    }

    public function available(): bool
    {
        return $this->config->isConfigured() && $this->config->provider() === 'openai';
    }

    /**
     * @throws AiException
     * @return string ruta absoluta temporal del archivo descargado
     */
    public function generateBackground(string $prompt): string
    {
        if (! $this->config->isConfigured()) {
            throw AiException::notConfigured();
        }

        if ($this->config->provider() !== 'openai') {
            throw AiException::unknown('La generación de imágenes con IA solo está disponible con el proveedor OpenAI por ahora. Cambia el proveedor en Configuración → IA o sube una imagen manualmente.');
        }

        try {
            $response = Http::withToken($this->config->apiKey())
                ->timeout(90)
                ->post("{$this->config->baseUrl()}/images/generations", [
                    'model' => 'dall-e-3',
                    'prompt' => $prompt,
                    'size' => '1024x1024',
                    'n' => 1,
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw AiException::networkError($e->getMessage());
        }

        if ($response->status() === 401 || $response->status() === 403) {
            throw AiException::invalidToken($response->json('error.message', ''));
        }

        if ($response->status() === 429) {
            throw AiException::rateLimited($response->json('error.message', ''));
        }

        if ($response->failed()) {
            throw AiException::unknown('HTTP '.$response->status().' — '.$response->json('error.message', $response->body()));
        }

        $imageUrl = $response->json('data.0.url');

        if (! $imageUrl) {
            throw AiException::unknown('El proveedor no devolvió ninguna imagen.');
        }

        $imageResponse = Http::timeout(60)->get($imageUrl);

        if ($imageResponse->failed()) {
            throw AiException::networkError('No se pudo descargar la imagen generada.');
        }

        $tempPath = storage_path('app/tmp/'.Str::uuid().'.png');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        file_put_contents($tempPath, $imageResponse->body());

        return $tempPath;
    }
}
