<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->meta_title ?: $product->name }}</title>
    <meta name="description" content="{{ $product->meta_description ?: $product->short_description }}">
    <link rel="stylesheet" href="{{ asset('build/app.css') }}?v={{ filemtime(public_path('build/app.css')) }}">
</head>
<body class="bg-white font-sans antialiased">
    <div class="mx-auto max-w-3xl px-6 py-12">
        <div class="mb-6 flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-blue-600">
            <span class="rounded-full bg-blue-50 px-3 py-1">Vista previa · no publicado</span>
            @if ($product->collection) <span class="text-slate-400">{{ $product->collection->name }}</span> @endif
        </div>

        @if ($product->main_image)
            <img src="{{ $product->imageUrl() }}" class="mb-8 aspect-video w-full rounded-2xl object-cover" alt="{{ $product->name }}">
        @endif

        <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ $product->name }}</h1>
        <p class="mt-3 text-lg text-slate-600">{{ $product->short_description }}</p>

        <div class="mt-6 flex items-center gap-4">
            <span class="text-2xl font-semibold text-slate-900">{{ $product->formattedPrice() }}</span>
            @if ($product->old_price)
                <span class="text-lg text-slate-400 line-through">${{ number_format($product->old_price, 2) }}</span>
            @endif
        </div>

        @if ($product->description)
            <div class="prose mt-8 max-w-none whitespace-pre-line text-slate-700">{{ $product->description }}</div>
        @endif

        @if ($product->benefits)
            <div class="mt-8">
                <h2 class="mb-3 text-lg font-semibold text-slate-900">Beneficios</h2>
                <div class="whitespace-pre-line text-slate-700">{{ $product->benefits }}</div>
            </div>
        @endif

        @if ($product->features)
            <div class="mt-8">
                <h2 class="mb-3 text-lg font-semibold text-slate-900">Características</h2>
                <div class="whitespace-pre-line text-slate-700">{{ $product->features }}</div>
            </div>
        @endif

        @if ($product->images->isNotEmpty())
            <div class="mt-8 grid grid-cols-3 gap-3">
                @foreach ($product->images as $img)
                    <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" class="aspect-square rounded-xl object-cover" alt="{{ $img->alt_text }}">
                @endforeach
            </div>
        @endif

        <div class="mt-10 flex gap-3">
            @if ($product->whatsapp_url)
                <a href="{{ $product->whatsapp_url }}" target="_blank" class="nodo-btn-primary">Contactar por WhatsApp</a>
            @endif
            @if ($product->demo_url)
                <a href="{{ $product->demo_url }}" target="_blank" class="nodo-btn-secondary">Ver demostración</a>
            @endif
        </div>
    </div>
</body>
</html>
