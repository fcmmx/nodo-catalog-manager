@php($content = $content ?? '')
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:20px 24px;color:#1E293B;font-size:15px;line-height:1.6;font-family:Arial,sans-serif;">
    {!! nl2br(e($content)) !!}
</td></tr>
</table>
