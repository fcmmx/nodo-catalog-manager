<?php

namespace App\Services\Email;

use App\Models\EmailCampaignSend;
use App\Models\Product;

class BlockRenderer
{
    /**
     * Renderiza los bloques del constructor visual a HTML compatible con
     * clientes de correo, envolviendo enlaces para el seguimiento de clics
     * y agregando el pie de baja obligatorio.
     */
    public function render(array $blocks, EmailCampaignSend $send): string
    {
        $html = '';

        foreach ($blocks as $block) {
            $html .= $this->renderBlock($block, $send);
        }

        $html .= view('emails.blocks.unsubscribe-footer', [
            'unsubscribeUrl' => route('email.unsubscribe.form', ['token' => $send->token]),
        ])->render();

        $pixelUrl = route('email.track.open', ['token' => $send->token]);

        return view('emails.layout', ['content' => $html, 'pixelUrl' => $pixelUrl])->render();
    }

    protected function renderBlock(array $block, EmailCampaignSend $send): string
    {
        $type = $block['type'] ?? null;

        return match ($type) {
            'header' => view('emails.blocks.header', $block)->render(),
            'text' => view('emails.blocks.text', $block)->render(),
            'image' => view('emails.blocks.image', ['block' => $block, 'link' => $this->wrapLink($block['link'] ?? null, $send)])->render(),
            'button' => view('emails.blocks.button', ['block' => $block, 'link' => $this->wrapLink($block['url'] ?? null, $send)])->render(),
            'products' => view('emails.blocks.products', ['products' => $this->productsFor($block), 'send' => $send])->render(),
            'divider' => view('emails.blocks.divider')->render(),
            'social' => view('emails.blocks.social', $block)->render(),
            'footer' => view('emails.blocks.footer', $block)->render(),
            default => '',
        };
    }

    protected function productsFor(array $block): \Illuminate\Support\Collection
    {
        $ids = $block['product_ids'] ?? [];

        if (empty($ids)) {
            return collect();
        }

        return Product::whereIn('id', $ids)->get();
    }

    public function wrapLink(?string $url, EmailCampaignSend $send): ?string
    {
        if (! $url) {
            return null;
        }

        return route('email.track.click', ['token' => $send->token, 'url' => base64_encode($url)]);
    }
}
