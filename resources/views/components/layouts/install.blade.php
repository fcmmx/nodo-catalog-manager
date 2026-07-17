@props(['title' => null, 'step' => 1])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Instalación · NODO Catalog Manager' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('build/app.css') }}?v={{ filemtime(public_path('build/app.css')) }}">
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased">
    <div class="relative min-h-screen overflow-hidden px-4 py-12">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(37,99,235,0.10),transparent_45%),radial-gradient(circle_at_80%_30%,rgba(124,58,237,0.10),transparent_40%)]"></div>

        <div class="relative mx-auto max-w-2xl">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">N</div>
                <h1 class="text-xl font-semibold tracking-tight text-slate-900">Instalación de NODO Catalog Manager</h1>
                <p class="mt-1 text-sm text-slate-500">Paso {{ $step }} de 5</p>
            </div>

            <div class="mb-6 flex items-center gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <div class="h-1.5 flex-1 rounded-full {{ $i <= $step ? 'bg-gradient-to-r from-blue-600 to-violet-600' : 'bg-slate-200' }}"></div>
                @endfor
            </div>

            <div class="nodo-card p-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} NODO 360 MARKETING TECHNOLOGY</p>
        </div>
    </div>
</body>
</html>
