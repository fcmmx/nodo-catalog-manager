<?php

namespace App\Services\Social;

use App\Models\SocialPost;
use App\Services\Social\Exceptions\SocialPublishException;

class SocialPublisher
{
    /**
     * Canales con publicación automática real implementada. Los demás
     * canales del brief (Instagram, LinkedIn, TikTok, X, Google Business)
     * quedan preparados para programarse y descargarse, pero requieren
     * publicación manual hasta que se implemente su conector específico —
     * no se simula ninguna llamada a esas APIs.
     */
    public const AUTOMATED_CHANNELS = ['facebook'];

    public function __construct(protected MetaGraphClient $metaClient)
    {
    }

    public function publish(SocialPost $post): void
    {
        $post->update(['status' => 'enviando']);

        try {
            if (! in_array($post->channel, self::AUTOMATED_CHANNELS)) {
                throw SocialPublishException::unsupportedChannel($post->channel);
            }

            if (! $post->account) {
                throw SocialPublishException::notAuthorized();
            }

            $externalId = $this->metaClient->publish($post->account, $post);

            $post->update([
                'status' => 'enviada',
                'result' => 'Publicado correctamente',
                'external_post_id' => $externalId,
                'error_message' => null,
            ]);
        } catch (SocialPublishException $e) {
            $post->update([
                'status' => $e->reason === SocialPublishException::REASON_UNSUPPORTED_CHANNEL
                    ? 'pendiente_autorizacion'
                    : 'error',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
