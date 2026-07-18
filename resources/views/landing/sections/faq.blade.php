@if (! empty($section['items']))
<section class="bg-slate-50 px-6 py-16">
    <div class="mx-auto max-w-3xl">
        @if (! empty($section['title']))
            <h2 class="text-center text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
        @endif
        <div class="mt-8 divide-y divide-slate-200 rounded-xl bg-white shadow-sm">
            @foreach ($section['items'] as $item)
                @if (! empty($item['heading']))
                    <details class="group p-5">
                        <summary class="cursor-pointer list-none font-semibold text-slate-900 marker:content-none">
                            {{ $item['heading'] }}
                        </summary>
                        @if (! empty($item['text']))
                            <p class="mt-2 text-sm text-slate-600">{{ $item['text'] }}</p>
                        @endif
                    </details>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
