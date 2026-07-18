@if (! empty($section['url']))
<section class="px-6 py-16">
    <div class="mx-auto max-w-4xl">
        <img src="{{ $section['url'] }}" alt="{{ $section['alt'] ?? '' }}" class="w-full rounded-2xl object-cover shadow-lg" loading="lazy">
    </div>
</section>
@endif
