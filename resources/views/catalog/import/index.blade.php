<x-layouts.app title="Importar / Exportar · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Importar / Exportar' => '']">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="nodo-card p-6 lg:col-span-1">
            <h2 class="mb-2 text-sm font-semibold text-slate-900 dark:text-white">Importar productos</h2>
            <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Sube un archivo CSV, XLSX, XLS o JSON con tu catálogo. Podrás mapear las columnas antes de procesar.</p>

            <a href="{{ route('catalog.import.template') }}" class="mb-4 block text-sm font-medium text-blue-600 hover:underline">Descargar plantilla CSV</a>

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('catalog.import.upload') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="nodo-label">Archivo</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls,.json" required class="nodo-input">
                    @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Si el SKU ya existe</label>
                    <select name="duplicate_strategy" class="nodo-input">
                        <option value="skip">Omitir (no modificar)</option>
                        <option value="update">Actualizar producto existente</option>
                    </select>
                </div>
                <button type="submit" class="nodo-btn-primary w-full">Subir y continuar</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Historial de importaciones</h2>
            @if ($batches->isEmpty())
                <x-ui.empty-state title="Todavía no has importado archivos" description="Cuando subas un archivo, aquí verás el progreso y los reportes de error." />
            @else
                <div class="nodo-card overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                            <tr>
                                <th class="px-4 py-3">Archivo</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3">Progreso</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3 text-right">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($batches as $batch)
                                <tr>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $batch->original_filename }}</td>
                                    <td class="px-4 py-3">
                                        <span class="nodo-badge {{ match(true) { str_contains($batch->status,'completado_con_errores') => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300', $batch->status === 'completado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', $batch->status === 'fallido' => 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300', default => 'bg-slate-100 text-slate-600 dark:bg-slate-800' } }}">{{ ucfirst(str_replace('_', ' ', $batch->status)) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $batch->processed_rows }}/{{ $batch->total_rows }}</td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('catalog.import.show', $batch) }}" class="text-xs font-medium text-blue-600 hover:underline">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $batches->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>
