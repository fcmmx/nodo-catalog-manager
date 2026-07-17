<x-layouts.app title="Detalle de importación · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Importar / Exportar' => route('catalog.import.index'), 'Detalle' => '']">
    @if (in_array($batch->status, ['processing', 'mapeo_pendiente']))
        <meta http-equiv="refresh" content="4">
    @endif

    <div class="nodo-card p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $batch->original_filename }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Subido {{ $batch->created_at->diffForHumans() }} por {{ $batch->user?->name }}</p>
            </div>
            <span class="nodo-badge {{ match(true) { str_contains($batch->status,'completado_con_errores') => 'bg-amber-50 text-amber-700', $batch->status === 'completado' => 'bg-emerald-50 text-emerald-700', $batch->status === 'fallido' => 'bg-red-50 text-red-700', default => 'bg-slate-100 text-slate-600' } }}">{{ ucfirst(str_replace('_', ' ', $batch->status)) }}</span>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $batch->total_rows }}</p>
                <p class="text-xs text-slate-500">Total de filas</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $batch->processed_rows }}</p>
                <p class="text-xs text-slate-500">Procesadas</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-4 text-center dark:bg-emerald-950">
                <p class="text-2xl font-semibold text-emerald-700 dark:text-emerald-300">{{ $batch->success_rows }}</p>
                <p class="text-xs text-emerald-600">Exitosas</p>
            </div>
            <div class="rounded-xl bg-red-50 p-4 text-center dark:bg-red-950">
                <p class="text-2xl font-semibold text-red-700 dark:text-red-300">{{ $batch->error_rows }}</p>
                <p class="text-xs text-red-600">Con error</p>
            </div>
        </div>

        @if ($batch->total_rows > 0)
            <div class="mb-6 h-2 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full bg-gradient-to-r from-blue-600 to-violet-600" style="width: {{ min(100, round($batch->processed_rows / max($batch->total_rows,1) * 100)) }}%"></div>
            </div>
        @endif

        @if ($batch->errors_file_path)
            <a href="{{ route('catalog.import.errors', $batch) }}" class="nodo-btn-secondary mb-6 inline-flex">Descargar reporte de errores</a>
        @endif

        @if (!empty($batch->errors))
            <h2 class="mb-2 text-sm font-semibold text-slate-900 dark:text-white">Errores encontrados</h2>
            <div class="max-h-80 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-800">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50"><tr><th class="px-3 py-2 text-left">Fila</th><th class="px-3 py-2 text-left">Error</th></tr></thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($batch->errors as $e)
                            <tr><td class="px-3 py-2">{{ $e['fila'] }}</td><td class="px-3 py-2 text-red-600">{{ $e['error'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('catalog.import.index') }}" class="nodo-btn-secondary">Volver al historial</a>
        </div>
    </div>
</x-layouts.app>
