<?php

namespace App\Console\Commands;

use App\Jobs\SendCampaignEmailJob;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignSend;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendDueEmailCampaigns extends Command
{
    protected $signature = 'email:send-due-campaigns';

    protected $description = 'Prepara y encola el envío de campañas de email programadas cuya fecha ya llegó (pensado para ejecutarse por cron cada minuto)';

    public function handle(): int
    {
        $campaigns = EmailCampaign::whereIn('status', ['programada', 'enviando'])
            ->where(function ($q) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->get();

        foreach ($campaigns as $campaign) {
            $this->prepareSends($campaign);
            $this->dispatchBatch($campaign);
        }

        if ($campaigns->isEmpty()) {
            $this->info('No hay campañas pendientes de enviar.');
        }

        return self::SUCCESS;
    }

    /**
     * Crea el registro de envío (pendiente) para cada contacto suscrito y
     * con consentimiento de la lista de la campaña, la primera vez que se
     * procesa (funciona como la "cola" de la campaña).
     */
    protected function prepareSends(EmailCampaign $campaign): void
    {
        if ($campaign->sends()->exists()) {
            return;
        }

        $contacts = $campaign->list?->contacts()->where('subscribed', true)->where('consent', true)->get() ?? collect();

        foreach ($contacts as $contact) {
            EmailCampaignSend::create([
                'email_campaign_id' => $campaign->id,
                'contact_id' => $contact->id,
                'token' => Str::random(48),
                'status' => 'pendiente',
            ]);
        }

        $campaign->update(['status' => 'enviando']);
        $this->info("Campaña #{$campaign->id} ({$campaign->name}): {$contacts->count()} destinatario(s) en cola.");
    }

    protected function dispatchBatch(EmailCampaign $campaign): void
    {
        $campaign->refresh();

        $pending = $campaign->sends()->where('status', 'pendiente')->limit($campaign->batch_limit)->get();

        foreach ($pending as $send) {
            SendCampaignEmailJob::dispatch($send->id);
        }

        $this->info("Campaña #{$campaign->id}: {$pending->count()} correo(s) encolado(s) en este lote.");

        if ($campaign->sends()->where('status', 'pendiente')->doesntExist()) {
            $campaign->update(['status' => 'enviada', 'sent_at' => now()]);
        }
    }
}
