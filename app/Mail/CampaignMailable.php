<?php

namespace App\Mail;

use App\Models\EmailCampaignSend;
use App\Services\Email\BlockRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EmailCampaignSend $send)
    {
    }

    public function build(): static
    {
        $campaign = $this->send->campaign;
        $html = app(BlockRenderer::class)->render($campaign->blocks ?? [], $this->send);

        return $this
            ->from($campaign->from_email, $campaign->from_name)
            ->subject($campaign->subject)
            ->html($html);
    }
}
