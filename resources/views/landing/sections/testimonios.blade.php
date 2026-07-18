@if (! empty($section['items']))
<section class="px-6 py-16">
    <div class="mx-auto max-w-5xl">
        @if (! empty($section['title']))
            <h2 class="text-center text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
        @endif
        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($section['items'] as $item)
                @if (! empty($item['text']))
                    <blockquote class="rounded-xl border border-slate-100 p-6 shadow-sm">
                        <p class="text-slate-600">&ldquo;{{ $item['text'] }}&rdquo;</p>
                        @if (! empty($item['heading']))
                            <footer class="mt-4 text-sm font-semibold text-slate-900">— {{ $item['heading'] }}</footer>
                        @endif
                    </blockquote>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
