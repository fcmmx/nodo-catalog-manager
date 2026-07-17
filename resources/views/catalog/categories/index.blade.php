<x-layouts.app title="Categorías · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Categorías' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Categorías</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Subdivisiones dentro de tus colecciones para organizar el catálogo con más detalle.</p>
        </div>
        @can('crear categorias')
            <a href="{{ route('catalog.categories.create') }}" class="nodo-btn-primary">+ Nueva categoría</a>
        @endcan
    </div>

    @if ($categories->isEmpty())
        <x-ui.empty-state title="Todavía no hay categorías" description="Las categorías son opcionales; úsalas cuando necesites más detalle que las colecciones." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Colección</th>
                        <th class="px-4 py-3">Productos</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($categories as $category)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $category->name }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $category->collection?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $category->products_count }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">
                                    {{ $category->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    @can('editar categorias')
                                        <a href="{{ route('catalog.categories.edit', $category) }}" class="text-xs font-medium text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('eliminar categorias')
                                        <form method="POST" action="{{ route('catalog.categories.destroy', $category) }}" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $categories->links() }}</div>
    @endif
</x-layouts.app>
