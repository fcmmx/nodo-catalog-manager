@php
    $categories = [
        'seo' => ['label' => 'SEO tradicional', 'color' => '#2563EB'],
        'aeo' => ['label' => 'AEO (motores de respuesta por IA)', 'color' => '#7C3AED'],
        'geo' => ['label' => 'GEO (motores generativos)', 'color' => '#0EA5E9'],
    ];
@endphp
<x-layouts.app :title="'Auditoría · '.$audit->url.' · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Auditor IA-Ready' => route('seo-audit.index'), 'Resultado' => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white break-all">{{ $audit->url }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Analizado el {{ $audit->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('seo-audit.index') }}" class="nodo-btn-secondary">← Historial</a>
            @if ($audit->status === 'completado')
                <a href="{{ route('seo-audit.pdf', $audit) }}" class="nodo-btn-primary">Descargar PDF</a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    @if ($audit->status === 'error')
        <div class="nodo-card p-6">
            <div class="flex items-start gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <div>
                    <p class="font-medium">No se pudo completar la auditoría.</p>
                    <p class="mt-1">{{ $audit->error_message }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="nodo-card p-5 text-center">
                <p class="text-xs uppercase tracking-wide text-slate-400">Calificación general</p>
                <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-white">{{ $audit->score }}<span class="text-base font-normal text-slate-400">/100</span></p>
                <p class="text-sm font-semibold" style="color: {{ $audit->score >= 75 ? '#16A34A' : ($audit->score >= 40 ? '#F59E0B' : '#DC2626') }}">Nivel {{ $audit->grade() }}</p>
            </div>
            @foreach ($categories as $key => $meta)
                <div class="nodo-card p-5 text-center">
                    <p class="text-xs uppercase tracking-wide text-slate-400">{{ $meta['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold text-slate-900 dark:text-white">{{ $audit->results[$key]['score'] }}<span class="text-base font-normal text-slate-400">/{{ $audit->results[$key]['max'] }}</span></p>
                </div>
            @endforeach
        </div>

        @foreach ($categories as $key => $meta)
            <div class="nodo-card mb-6 p-6">
                <h2 class="mb-4 flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                    <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $meta['color'] }}"></span>
                    {{ $meta['label'] }} — {{ $audit->results[$key]['score'] }}/{{ $audit->results[$key]['max'] }} puntos
                </h2>
                <div class="space-y-3">
                    @foreach ($audit->results[$key]['checks'] as $check)
                        <div class="flex items-start gap-3 rounded-lg border border-slate-100 p-3 dark:border-slate-800">
                            <span class="mt-0.5 shrink-0 text-lg">{{ $check['passed'] ? '✅' : '⚠️' }}</span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $check['label'] }}</p>
                                    <span class="text-xs font-semibold text-slate-400">{{ $check['points'] }}/{{ $check['max'] }}</span>
                                </div>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $check['detail'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</x-layouts.app>
