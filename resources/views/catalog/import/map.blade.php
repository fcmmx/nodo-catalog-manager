<x-layouts.app title="Mapear columnas · Importación" :breadcrumbs="['Dashboard' => route('dashboard'), 'Importar / Exportar' => route('catalog.import.index'), 'Mapear columnas' => '']">
    <div class="nodo-card p-6">
        <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Relaciona las columnas de tu archivo</h1>
        <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">{{ $batch->original_filename }} — {{ $batch->total_rows }} fila(s) detectada(s). Relaciona cada columna del archivo con un campo del sistema.</p>

        <form method="POST" action="{{ route('catalog.import.process', $batch) }}">
            @csrf
            <div class="mb-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-500">
                            <th class="py-2 pr-4">Campo del sistema</th>
                            <th class="py-2 pr-4">Columna del archivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($fields as $field => $label)
                            <tr>
                                <td class="py-2 pr-4 font-medium text-slate-700 dark:text-slate-200">{{ $label }}</td>
                                <td class="py-2 pr-4">
                                    <select name="mapping[{{ $field }}]" class="nodo-input">
                                        <option value="">No importar</option>
                                        @foreach ($headers as $i => $header)
                                            <option value="{{ $i }}" {{ strcasecmp($header, $field) === 0 ? 'selected' : '' }}>{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h2 class="mb-2 text-sm font-semibold text-slate-900 dark:text-white">Vista previa (primeras filas)</h2>
            <div class="mb-6 overflow-x-auto rounded-lg border border-slate-200 dark:border-slate-800">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>@foreach ($headers as $h)<th class="whitespace-nowrap px-3 py-2 text-left">{{ $h }}</th>@endforeach</tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($previewRows as $row)
                            <tr>@foreach ($row as $cell)<td class="whitespace-nowrap px-3 py-2 text-slate-500">{{ $cell }}</td>@endforeach</tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
            @endif

            <div class="flex justify-between">
                <a href="{{ route('catalog.import.index') }}" class="nodo-btn-secondary">Cancelar</a>
                <button type="submit" class="nodo-btn-primary">Iniciar importación</button>
            </div>
        </form>
    </div>
</x-layouts.app>
