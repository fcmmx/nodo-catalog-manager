@php($links = array_filter(['Facebook' => $facebook ?? null, 'Instagram' => $instagram ?? null, 'LinkedIn' => $linkedin ?? null, 'X' => $x ?? null]))
@if (! empty($links))
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:12px 24px;text-align:center;font-family:Arial,sans-serif;">
    @foreach ($links as $label => $url)
        <a href="{{ $url }}" style="margin:0 8px;color:#2563EB;text-decoration:none;font-size:13px;">{{ $label }}</a>
    @endforeach
</td></tr>
</table>
@endif
