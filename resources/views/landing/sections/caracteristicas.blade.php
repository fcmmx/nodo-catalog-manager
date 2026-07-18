@if (! empty($section['items']))
<section class="bg-slate-50 px-6 py-16">
    <div class="mx-auto max-w-5xl">
        @if (! empty($section['title']))
            <h2 class="text-center text-2xl font-bold text-slate-900">{{ $section['title'] }}</h2>
        @endif
        <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($section['items'] as $item)
                @if (! empty($item['heading']) || ! empty($item['text']))
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        @if (! empty($item['heading']))
                            <p class="font-semibold text-slate-900">{{ $item['heading'] }}</p>
                        @endif
                        @if (! empty($item['text']))
                            <p class="mt-1 text-sm text-slate-600">{{ $item['text'] }}</p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
