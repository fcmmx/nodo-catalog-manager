@php($renderer = app(\App\Services\Email\BlockRenderer::class))
@if ($products->isNotEmpty())
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:0 24px 20px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        @foreach ($products as $product)
            <td width="{{ (int) (100 / max(1, min(3, $products->count()))) }}%" style="padding:6px;vertical-align:top;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0;border-radius:8px;">
                    @if ($product->main_image)
                        <tr><td><img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" width="100%" style="border-radius:8px 8px 0 0;display:block;"></td></tr>
                    @endif
                    <tr><td style="padding:10px;font-family:Arial,sans-serif;">
                        <p style="margin:0 0 4px;font-size:13px;font-weight:bold;color:#1E293B;">{{ $product->name }}</p>
                        <p style="margin:0 0 8px;font-size:12px;color:#64748B;">{{ $product->formattedPrice() }}</p>
                        <a href="{{ $renderer->wrapLink($product->url ?: $product->whatsapp_url, $send) }}" style="font-size:12px;color:#2563EB;text-decoration:none;font-weight:bold;">Ver más →</a>
                    </td></tr>
                </table>
            </td>
        @endforeach
    </tr>
    </table>
</td></tr>
</table>
@endif
