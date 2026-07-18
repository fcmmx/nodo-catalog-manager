<?php

namespace App\Services\Commerce;

use Illuminate\Support\Facades\Http;

/**
 * Cliente real de la Graph API de Meta para verificar la conexión con
 * un catálogo de Meta Commerce Manager (Facebook/Instagram Shops).
 * Solo hace una consulta de lectura (GET) para confirmar que el ID de
 * catálogo y el token de acceso son válidos — no publica ni modifica
 * nada, ya que la sincronización real de productos ocurre por el feed
 * público (CSV/XML) que Meta lee de forma periódica.
 */
class MetaCatalogClient
{
    protected string $baseUrl = 'https://graph.facebook.com/v19.0';

    /**
     * @return array{name: string, product_count: int}
     *
     * @throws MetaCommerceException
     */
    public function verifyCatalog(string $catalogId, string $accessToken): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/{$catalogId}", [
                'fields' => 'name,product_count',
                'access_token' => $accessToken,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw MetaCommerceException::networkError($e->getMessage());
        }

        if ($response->status() === 401 || $response->status() === 190) {
            throw MetaCommerceException::invalidToken();
        }

        if ($response->failed()) {
            throw MetaCommerceException::apiError($response->json('error.message', $response->body()));
        }

        return [
            'name' => $response->json('name', ''),
            'product_count' => (int) $response->json('product_count', 0),
        ];
    }
}
