<x-layouts.app title="Actividad del sistema · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Actividad del sistema' => '']">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Actividad del sistema</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Registro de inicios de sesión, cambios y acciones relevantes.</p>
    </div>

    <form method="GET" class="mb-4 flex gap-3">
        <select name="log_name" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            @foreach ($logNames as $name)
                <option value="{{ $name }}" {{ request('log_name') === $name ? 'selected' : '' }}>{{ ucfirst($name) }}</option>
            @endforeach
        </select>
    </form>

    @if ($activities->isEmpty())
        <x-ui.empty-state title="Sin actividad registrada" />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Categoría</th>
                        <th class="px-4 py-3">Descripción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($activities as $log)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $log->causer?->name ?? 'Sistema' }}</td>
                            <td class="px-4 py-3"><span class="nodo-badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $log->log_name }}</span></td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $activities->links() }}</div>
    @endif
</x-layouts.app>
