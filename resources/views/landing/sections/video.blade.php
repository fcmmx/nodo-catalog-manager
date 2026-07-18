@php
    $embedUrl = null;
    $videoUrl = $section['video_url'] ?? null;
    if ($videoUrl) {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([a-zA-Z0-9_-]+)/', $videoUrl, $m)) {
            $embedUrl = 'https://www.youtube.com/embed/'.$m[1];
        } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $m)) {
            $embedUrl = 'https://player.vimeo.com/video/'.$m[1];
        }
    }
@endphp
@if ($embedUrl)
<section class="px-6 py-16">
    <div class="mx-auto max-w-4xl">
        <div class="aspect-video overflow-hidden rounded-2xl shadow-lg">
            <iframe src="{{ $embedUrl }}" class="h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        </div>
    </div>
</section>
@endif
