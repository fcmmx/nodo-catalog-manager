<?php

namespace App\Jobs;

use App\Mail\CampaignMailable;
use App\Models\EmailCampaignSend;
use App\Services\Email\EmailConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(protected int $sendId)
    {
    }

    public function handle(EmailConfig $config): void
    {
        $send = EmailCampaignSend::with(['campaign', 'contact'])->findOrFail($this->sendId);

        if ($send->status !== 'pendiente') {
            return;
        }

        if (! $send->contact->subscribed || ! $send->contact->consent) {
            $send->update(['status' => 'error', 'error_message' => 'El contacto no tiene consentimiento vigente o está dado de baja.']);

            return;
        }

        if (! $config->isConfigured()) {
            $send->update(['status' => 'error', 'error_message' => 'No hay un proveedor de email marketing configurado.']);

            return;
        }

        $config->applyRuntimeConfig();

        try {
            Mail::mailer($config->mailerName())->to($send->contact->email)->send(new CampaignMailable($send));

            $send->update(['status' => 'enviado', 'sent_at' => now()]);
            $send->campaign->increment('sent_count');
        } catch (\Throwable $e) {
            $send->update(['status' => 'error', 'error_message' => $e->getMessage()]);
            $send->campaign->increment('bounce_count');
        }
    }
}
