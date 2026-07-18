@if (! empty($section['content']))
<section class="px-6 py-16">
    <div class="mx-auto max-w-3xl">
        @if (! empty($section['title']))
            <h2 class="text-center text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
        @endif
        <div class="mt-6 text-slate-600 leading-relaxed">
            {!! nl2br(e($section['content'])) !!}
        </div>
    </div>
</section>
@endif
