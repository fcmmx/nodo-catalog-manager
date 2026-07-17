<x-layouts.app title="Productos y servicios · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Catálogo' => route('catalog.products.index')]">
    <div x-data="{ selected: [] }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Productos y servicios</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $products->total() }} resultado(s)</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('exportar productos')
                    <a href="{{ route('catalog.products.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="nodo-btn-secondary">Exportar CSV</a>
                    <a href="{{ route('catalog.products.export', array_merge(request()->query(), ['format' => 'json'])) }}" class="nodo-btn-secondary">Exportar JSON</a>
                @endcan
                @can('crear productos')
                    <a href="{{ route('catalog.products.create') }}" class="nodo-btn-primary">+ Nuevo producto</a>
                @endcan
            </div>
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o SKU…" class="nodo-input max-w-xs">
            <select name="status" class="nodo-input w-auto" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach (\App\Models\Product::STATUSES as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="collection_id" class="nodo-input w-auto" onchange="this.form.submit()">
                <option value="">Todas las colecciones</option>
                @foreach ($collections as $c)
                    <option value="{{ $c->id }}" {{ (string) request('collection_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="category_id" class="nodo-input w-auto" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ (string) request('category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 rounded-lg border border-slate-300 px-3 text-sm dark:border-slate-700">
                <input type="checkbox" name="trashed" value="1" {{ request('trashed') === '1' ? 'checked' : '' }} onchange="this.form.submit()" class="rounded border-slate-300 text-blue-600">
                Papelera
            </label>
            <button type="submit" class="nodo-btn-secondary">Filtrar</button>
        </form>

        <div x-show="selected.length > 0" x-cloak class="nodo-card mb-4 flex flex-wrap items-center gap-3 p-3" x-data="{ action: '', value: '' }">
            <span class="text-sm font-medium text-slate-700 dark:text-slate-200" x-text="selected.length + ' seleccionado(s)'"></span>
            <select x-model="action" class="nodo-input w-auto">
                <option value="">Elegir acción masiva…</option>
                <option value="status">Cambiar estado</option>
                <option value="price">Cambiar precio</option>
                <option value="category">Cambiar categoría</option>
                <option value="collection">Cambiar colección</option>
                <option value="availability">Cambiar disponibilidad</option>
                <option value="featured">Marcar como destacado</option>
                <option value="tags">Reemplazar etiquetas</option>
                <option value="delete">Eliminar seleccionados</option>
            </select>

            <template x-if="action === 'status'">
                <select x-model="value" class="nodo-input w-auto">
                    @foreach (\App\Models\Product::STATUSES as $s)<option value="{{ $s }}">{{ ucfirst($s) }}</option>@endforeach
                </select>
            </template>
            <template x-if="action === 'availability'">
                <select x-model="value" class="nodo-input w-auto">
                    @foreach (\App\Models\Product::AVAILABILITIES as $a)<option value="{{ $a }}">{{ ucfirst(str_replace('_',' ',$a)) }}</option>@endforeach
                </select>
            </template>
            <template x-if="action === 'category'">
                <select x-model="value" class="nodo-input w-auto">
                    <option value="">Sin categoría</option>
                    @foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </template>
            <template x-if="action === 'collection'">
                <select x-model="value" class="nodo-input w-auto">
                    <option value="">Sin colección</option>
                    @foreach ($collections as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </template>
            <template x-if="action === 'price'">
                <input type="number" step="0.01" x-model="value" placeholder="Nuevo precio" class="nodo-input w-auto">
            </template>
            <template x-if="action === 'tags'">
                <input type="text" x-model="value" placeholder="etiqueta1, etiqueta2" class="nodo-input w-auto">
            </template>
            <template x-if="action === 'featured'">
                <select x-model="value" class="nodo-input w-auto">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </template>

            <form method="POST" action="{{ route('catalog.products.bulk') }}" @submit="if (action==='delete' && !confirm('¿Eliminar los productos seleccionados?')) $event.preventDefault();">
                @csrf
                <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
                <input type="hidden" name="action" :value="action">
                <input type="hidden" name="value" :value="value">
                <button type="submit" class="nodo-btn-primary" :disabled="!action">Aplicar</button>
            </form>
        </div>

        @if ($products->isEmpty())
            <x-ui.empty-state title="No se encontraron productos" description="Ajusta los filtros o crea tu primer producto." />
        @else
            <div class="nodo-card overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3"><input type="checkbox" @change="selected = $event.target.checked ? [{{ $products->pluck('id')->implode(',') }}] : []" class="rounded border-slate-300"></th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Colección</th>
                            <th class="px-4 py-3">Precio</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Disponibilidad</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($products as $product)
                            <tr>
                                <td class="px-4 py-3"><input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="rounded border-slate-300"></td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                                </td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $product->collection?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">{{ $product->formattedPrice() }}</td>
                                <td class="px-4 py-3">
                                    <span class="nodo-badge {{ match($product->status) { 'activo' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'borrador' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300', 'archivado' => 'bg-slate-200 text-slate-600 dark:bg-slate-700', default => 'bg-slate-100 text-slate-500 dark:bg-slate-800' } }}">{{ ucfirst($product->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $product->availability)) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2 text-xs font-medium">
                                        @if ($product->trashed())
                                            <form method="POST" action="{{ route('catalog.products.restore', $product->id) }}">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:underline">Restaurar</button>
                                            </form>
                                        @else
                                            <a href="{{ route('catalog.products.preview', $product) }}" class="text-slate-500 hover:underline" target="_blank">Vista previa</a>
                                            @can('editar productos')
                                                <a href="{{ route('catalog.products.edit', $product) }}" class="text-blue-600 hover:underline">Editar</a>
                                            @endcan
                                            @can('crear productos')
                                                <form method="POST" action="{{ route('catalog.products.duplicate', $product) }}">
                                                    @csrf
                                                    <button type="submit" class="text-slate-500 hover:underline">Duplicar</button>
                                                </form>
                                            @endcan
                                            @can('eliminar productos')
                                                <form method="POST" action="{{ route('catalog.products.destroy', $product) }}" onsubmit="return confirm('¿Enviar este producto a la papelera?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
        @endif
    </div>
</x-layouts.app>
