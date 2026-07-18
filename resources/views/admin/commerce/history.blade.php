<x-layouts.app title="Historial de sincronización · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Meta Commerce' => route('admin.commerce.settings.edit'), 'Historial' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Historial de sincronización</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Cada vez que el feed se solicita (por Meta u otra plataforma) o se prueba la conexión, queda registrado aquí.</p>
        </div>
        <a href="{{ route('admin.commerce.settings.edit') }}" class="nodo-btn-secondary">← Configuración</a>
    </div>

    @if ($logs->isEmpty())
        <x-ui.empty-state title="Todavía no hay registros" description="En cuanto se solicite el feed o se pruebe la conexión con Meta, aparecerá aquí." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Productos</th>
                        <th class="px-4 py-3">Detalle</th>
                        <th class="px-4 py-3">IP</th>
                        <th class="px-4 py-3">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ match($log->type) { 'feed_csv' => 'Feed CSV', 'feed_xml' => 'Feed XML', 'connection_test' => 'Prueba de conexión', default => $log->type } }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ $log->status === 'exitoso' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300' }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->products_count ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->message ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $log->ip_address ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</x-layouts.app>
