<x-layouts.app title="Colecciones · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Colecciones' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Colecciones</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Agrupa tus productos y servicios en las grandes líneas de negocio de NODO 360.</p>
        </div>
        @can('crear colecciones')
            <a href="{{ route('catalog.collections.create') }}" class="nodo-btn-primary">+ Nueva colección</a>
        @endcan
    </div>

    @if ($collections->isEmpty())
        <x-ui.empty-state title="Todavía no hay colecciones" description="Crea tu primera colección para empezar a organizar el catálogo." />
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($collections as $collection)
                <div class="nodo-card p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="h-8 w-8 rounded-lg" style="background: {{ $collection->color ?? '#2563EB' }}"></span>
                        <span class="nodo-badge {{ $collection->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">
                            {{ $collection->is_active ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <h3 class="font-medium text-slate-900 dark:text-white">{{ $collection->name }}</h3>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $collection->description }}</p>
                    <p class="mt-3 text-xs text-slate-400">{{ $collection->products_count }} producto(s)</p>
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('catalog.products.index', ['collection_id' => $collection->id]) }}" class="nodo-btn-secondary flex-1 text-xs">Ver productos</a>
                        @can('editar colecciones')
                            <a href="{{ route('catalog.collections.edit', $collection) }}" class="nodo-btn-secondary text-xs">Editar</a>
                        @endcan
                        @can('eliminar colecciones')
                            <form method="POST" action="{{ route('catalog.collections.destroy', $collection) }}" onsubmit="return confirm('¿Eliminar esta colección? Esta acción no se puede deshacer.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="nodo-btn-secondary text-xs text-red-600">Eliminar</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $collections->links() }}</div>
    @endif
</x-layouts.app>
