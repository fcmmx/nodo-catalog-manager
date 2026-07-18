<x-layouts.app title="Landing Pages · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Landing Pages' => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Landing Pages</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $landings->total() }} landing page(s)</p>
        </div>
        @can('crear landing')
            <a href="{{ route('landing.create') }}" class="nodo-btn-primary">+ Nueva landing page</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <select name="status" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    @if ($landings->isEmpty())
        <x-ui.empty-state title="No hay landing pages todavía" description="Crea una landing page para un producto o campaña, arma su contenido y publícala con su propia URL." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Vistas</th>
                        <th class="px-4 py-3">Prospectos</th>
                        <th class="px-4 py-3">Conversión</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($landings as $landing)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $landing->name }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $landing->product?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ match($landing->status) { 'publicada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'archivada' => 'bg-slate-100 text-slate-600 dark:bg-slate-800', default => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' } }}">{{ $statuses[$landing->status] ?? $landing->status }}</span>
                                @if ($landing->isPublished())
                                    <a href="{{ $landing->publicUrl() }}" target="_blank" class="ml-1 text-xs text-blue-600 hover:underline">ver ↗</a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $landing->views_count }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                                <a href="{{ route('landing.leads', $landing) }}" class="hover:underline">{{ $landing->leads_count }}</a>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $landing->conversionRate() }}%</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    @can('publicar landing')
                                        @if ($landing->isPublished())
                                            <form method="POST" action="{{ route('landing.unpublish', $landing) }}">
                                                @csrf
                                                <button type="submit" class="text-amber-600 hover:underline">Despublicar</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('landing.publish', $landing) }}">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:underline">Publicar</button>
                                            </form>
                                        @endif
                                    @endcan
                                    @can('crear landing')
                                        <form method="POST" action="{{ route('landing.duplicate', $landing) }}">
                                            @csrf
                                            <button type="submit" class="text-slate-500 hover:underline">Duplicar</button>
                                        </form>
                                    @endcan
                                    @can('editar landing')
                                        <a href="{{ route('landing.edit', $landing) }}" class="text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('eliminar landing')
                                        <form method="POST" action="{{ route('landing.destroy', $landing) }}" onsubmit="return confirm('¿Eliminar esta landing page?');">
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
        <div class="mt-4">{{ $landings->links() }}</div>
    @endif
</x-layouts.app>
