<x-layouts.app title="Historial de IA · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Historial de uso de IA' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Historial de uso de IA</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Registro de cada solicitud: usuario, fecha, producto, modelo, tokens y costo aproximado.</p>
        </div>
        @can('usar ia')
            <a href="{{ route('ai.generator') }}" class="nodo-btn-primary">Generador de contenido</a>
        @endcan
    </div>

    <form method="GET" class="mb-4 flex gap-3">
        <select name="task" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todas las tareas</option>
            @foreach ($tasks as $key => $task)
                <option value="{{ $key }}" {{ request('task') === $key ? 'selected' : '' }}>{{ $task['label'] }}</option>
            @endforeach
        </select>
        <select name="status" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
            <option value="aprobado" {{ request('status') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
            <option value="rechazado" {{ request('status') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
            <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Con error</option>
        </select>
    </form>

    @if ($generations->isEmpty())
        <x-ui.empty-state title="Todavía no se ha generado contenido con IA" description="Cuando uses el generador, cada solicitud quedará registrada aquí." />
    @else
        <div class="nodo-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Tarea</th>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Modelo</th>
                        <th class="px-4 py-3">Tokens</th>
                        <th class="px-4 py-3">Costo aprox.</th>
                        <th class="px-4 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($generations as $log)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $log->user?->name }}</td>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $tasks[$log->task]['label'] ?? $log->task }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->product?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->model }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->input_tokens ? $log->input_tokens.' / '.$log->output_tokens : '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $log->estimated_cost ? '$'.number_format($log->estimated_cost, 4) : '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ match($log->status) { 'aprobado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'rechazado' => 'bg-slate-200 text-slate-600 dark:bg-slate-700', 'error' => 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300', default => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300' } }}">{{ ucfirst($log->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $generations->links() }}</div>
    @endif
</x-layouts.app>
