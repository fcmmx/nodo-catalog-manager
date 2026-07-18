@if (! empty($section['items']))
<section class="px-6 py-16">
    <div class="mx-auto max-w-3xl">
        @if (! empty($section['title']))
            <h2 class="text-center text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
        @endif
        <div class="mt-8 space-y-4">
            @foreach ($section['items'] as $item)
                @if (! empty($item['text']))
                    <div class="rounded-xl border-l-4 border-red-400 bg-red-50 p-5">
                        @if (! empty($item['heading']))
                            <p class="font-semibold text-slate-900">{{ $item['heading'] }}</p>
                        @endif
                        <p class="mt-1 text-slate-600">{{ $item['text'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
