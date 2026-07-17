@php($title = $title ?? '')
@php($subtitle = $subtitle ?? '')
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="background:linear-gradient(135deg,#2563EB,#7C3AED);padding:32px 24px;text-align:center;">
    <h1 style="margin:0;color:#ffffff;font-size:22px;font-family:Arial,sans-serif;">{{ $title }}</h1>
    @if ($subtitle)
        <p style="margin:8px 0 0;color:#E2E8F0;font-size:14px;font-family:Arial,sans-serif;">{{ $subtitle }}</p>
    @endif
</td></tr>
</table>
