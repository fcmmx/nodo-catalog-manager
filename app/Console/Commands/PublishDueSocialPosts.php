<?php

namespace App\Console\Commands;

use App\Models\SocialPost;
use App\Services\Social\Exceptions\SocialPublishException;
use App\Services\Social\SocialPublisher;
use Illuminate\Console\Command;

class PublishDueSocialPosts extends Command
{
    protected $signature = 'social:publish-due';

    protected $description = 'Publica automáticamente las publicaciones programadas cuya fecha ya llegó (pensado para ejecutarse por cron cada minuto)';

    public function handle(SocialPublisher $publisher): int
    {
        $posts = SocialPost::where('status', 'programada')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No hay publicaciones pendientes de enviar.');

            return self::SUCCESS;
        }

        foreach ($posts as $post) {
            try {
                $publisher->publish($post);
                $this->info("Publicación #{$post->id} enviada correctamente ({$post->channel}).");
            } catch (SocialPublishException $e) {
                $this->warn("Publicación #{$post->id} no se pudo enviar automáticamente: {$e->getMessage()}");
            }
        }

        return self::SUCCESS;
    }
}
