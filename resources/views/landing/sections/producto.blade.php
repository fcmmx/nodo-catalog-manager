@if ($landing->product)
<section class="bg-slate-50 px-6 py-16">
    <div class="mx-auto grid max-w-5xl grid-cols-1 items-center gap-10 lg:grid-cols-2">
        @if ($landing->product->imageUrl())
            <img src="{{ $landing->product->imageUrl() }}" alt="{{ $landing->product->name }}" class="w-full rounded-2xl object-cover shadow-lg">
        @endif
        <div>
            <h2 class="text-2xl font-bold text-slate-900">{{ $landing->product->name }}</h2>
            @if ($landing->product->short_description)
                <p class="mt-3 text-slate-600">{{ $landing->product->short_description }}</p>
            @endif
            @if ($landing->product->price !== null)
                <p class="mt-4 text-xl font-bold" style="color: {{ $primaryColor }}">{{ $landing->product->formattedPrice() }}</p>
            @endif
        </div>
    </div>
</section>
@endif
