@php($src = $block['url'] ?? null)
@php($alt = $block['alt'] ?? '')
@if ($src)
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:0 24px 20px;text-align:center;">
    @if ($link)
        <a href="{{ $link }}"><img src="{{ $src }}" alt="{{ $alt }}" width="552" style="max-width:100%;border-radius:8px;display:block;margin:0 auto;"></a>
    @else
        <img src="{{ $src }}" alt="{{ $alt }}" width="552" style="max-width:100%;border-radius:8px;display:block;margin:0 auto;">
    @endif
</td></tr>
</table>
@endif
