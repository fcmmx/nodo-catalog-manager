<?php

namespace App\Services\Social;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Services\Social\Exceptions\SocialPublishException;
use Illuminate\Support\Facades\Http;

/**
 * Cliente real de la Graph API de Meta para publicar en el feed de una
 * página de Facebook. Requiere que la cuenta tenga un access_token de
 * página válido con el permiso pages_manage_posts.
 */
class MetaGraphClient
{
    protected string $baseUrl = 'https://graph.facebook.com/v19.0';

    /**
     * @throws SocialPublishException
     */
    public function publish(SocialAccount $account, SocialPost $post): string
    {
        if (! $account->isAuthorized()) {
            throw $account->token_expires_at && $account->token_expires_at->isPast()
                ? SocialPublishException::tokenExpired()
                : SocialPublishException::notAuthorized();
        }

        $message = trim($post->content."\n\n".$post->hashtags);

        $payload = array_filter([
            'message' => $message,
            'link' => $post->link,
            'access_token' => $account->decrypted_access_token,
        ]);

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/{$account->external_account_id}/feed", $payload);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw SocialPublishException::networkError($e->getMessage());
        }

        if ($response->status() === 401 || $response->status() === 190) {
            throw SocialPublishException::tokenExpired();
        }

        if ($response->failed()) {
            throw SocialPublishException::apiError($response->json('error.message', $response->body()));
        }

        $id = $response->json('id');

        if (! $id) {
            throw SocialPublishException::apiError('La API no devolvió un identificador de publicación.');
        }

        return $id;
    }
}
