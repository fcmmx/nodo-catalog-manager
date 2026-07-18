@php
    $ctaUrl = $section['url'] ?? null;
    if (! $ctaUrl && $landing->capture_form_enabled) {
        $ctaUrl = '#contacto';
    }
@endphp
@if ($ctaUrl)
<section class="px-6 py-16 text-center">
    <a href="{{ $ctaUrl }}" @if (! str_starts_with($ctaUrl, '#')) target="_blank" rel="noopener" @endif class="inline-block rounded-xl px-10 py-4 text-lg font-semibold text-white shadow-lg" style="background: {{ $primaryColor }}">
        {{ $section['text'] ?? 'Quiero más información' }}
    </a>
</section>
@endif
