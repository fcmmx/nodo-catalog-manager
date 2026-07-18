<?php

namespace App\Services\Commerce;

use App\Models\Setting;
use Illuminate\Support\Str;

/**
 * Configuración de la conexión con Meta Commerce Manager (catálogo de
 * productos) y del feed público de catálogo (CSV/XML). El feed se
 * expone en una URL protegida por un token propio de alta entropía,
 * que es lo que Meta Commerce Manager (o cualquier otra plataforma)
 * necesita para programar la lectura periódica del catálogo.
 */
class CommerceFeedConfig
{
    public function metaCatalogId(): ?string
    {
        return Setting::get('meta_catalog_id');
    }

    public function metaAccessToken(): ?string
    {
        return Setting::get('meta_access_token');
    }

    public function metaBusinessId(): ?string
    {
        return Setting::get('meta_business_id');
    }

    public function isMetaConfigured(): bool
    {
        return ! empty($this->metaCatalogId()) && ! empty($this->metaAccessToken());
    }

    public function feedToken(): string
    {
        $token = Setting::get('commerce_feed_token');

        if (! $token) {
            $token = Str::random(48);
            Setting::set('commerce_feed_token', $token, 'commerce');
        }

        return $token;
    }

    public function regenerateFeedToken(): string
    {
        $token = Str::random(48);
        Setting::set('commerce_feed_token', $token, 'commerce');

        return $token;
    }
}
