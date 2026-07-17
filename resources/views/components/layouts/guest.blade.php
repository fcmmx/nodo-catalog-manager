<!DOCTYPE html>
<html lang="es" x-data="{ dark: localStorage.getItem('nodo-theme') === 'dark' }" x-init="$watch('dark', v => { localStorage.setItem('nodo-theme', v ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', v); }); document.documentElement.classList.toggle('dark', dark);" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'NODO Catalog Manager' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('build/app.css') }}?v={{ filemtime(public_path('build/app.css')) }}">
    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased dark:bg-slate-950">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-12">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(37,99,235,0.12),transparent_45%),radial-gradient(circle_at_80%_30%,rgba(124,58,237,0.12),transparent_40%),radial-gradient(circle_at_50%_90%,rgba(220,38,38,0.08),transparent_40%)]"></div>

        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-violet-600 text-lg font-bold text-white shadow-lg shadow-blue-600/20">N</div>
                <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white">NODO Catalog Manager</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">El centro inteligente de contenidos, catálogos y automatización de NODO 360.</p>
            </div>

            <div class="nodo-card p-8">
                {{ $slot }}
            </div>

            <p class="mt-6 text-center text-xs text-slate-400 dark:text-slate-600">
                &copy; {{ date('Y') }} NODO 360 MARKETING TECHNOLOGY
            </p>
        </div>
    </div>
</body>
</html>
