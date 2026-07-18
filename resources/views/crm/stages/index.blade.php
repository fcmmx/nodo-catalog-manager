<x-layouts.app title="Etapas del CRM · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'CRM' => route('crm.index'), 'Etapas' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Etapas del pipeline</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Define las columnas del tablero CRM.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('crm.index') }}" class="nodo-btn-secondary">← Pipeline</a>
            @can('crear crm')
                <a href="{{ route('crm.stages.create') }}" class="nodo-btn-primary">+ Nueva etapa</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
    @endif

    <div class="nodo-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3">Orden</th>
                    <th class="px-4 py-3">Etapa</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Prospectos</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($stages as $stage)
                    <tr>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $stage->sort_order }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                            <span class="mr-2 inline-block h-2.5 w-2.5 rounded-full" style="background: {{ $stage->color }}"></span>
                            {{ $stage->name }}
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                            @if ($stage->is_won) Ganada @elseif ($stage->is_lost) Perdida @else Abierta @endif
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $stage->deals_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2 text-xs font-medium">
                                @can('editar crm')
                                    <a href="{{ route('crm.stages.edit', $stage) }}" class="text-blue-600 hover:underline">Editar</a>
                                @endcan
                                @can('eliminar crm')
                                    <form method="POST" action="{{ route('crm.stages.destroy', $stage) }}" onsubmit="return confirm('¿Eliminar esta etapa?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
