@php($text = $block['text'] ?? 'Ver más')
@if ($link)
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:8px 24px 24px;text-align:center;">
    <a href="{{ $link }}" style="display:inline-block;background:#DC2626;color:#ffffff;text-decoration:none;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;padding:12px 28px;border-radius:8px;">{{ $text }}</a>
</td></tr>
</table>
@endif
