<x-layouts.app :title="($collection->exists ? 'Editar' : 'Nueva').' colección · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Colecciones' => route('catalog.collections.index'), ($collection->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $collection->exists ? 'Editar colección' : 'Nueva colección' }}</h1>

            <form method="POST" action="{{ $collection->exists ? route('catalog.collections.update', $collection) : route('catalog.collections.store') }}" class="space-y-5">
                @csrf
                @if ($collection->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', $collection->name) }}" required class="nodo-input">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Descripción</label>
                    <textarea name="description" rows="3" class="nodo-input">{{ old('description', $collection->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="nodo-label">Ícono (heroicon)</label>
                        <input name="icon" value="{{ old('icon', $collection->icon) }}" class="nodo-input" placeholder="cpu-chip">
                    </div>
                    <div>
                        <label class="nodo-label">Color</label>
                        <input type="color" name="color" value="{{ old('color', $collection->color ?? '#2563EB') }}" class="nodo-input h-10 p-1">
                    </div>
                </div>
                <div>
                    <label class="nodo-label">Orden de aparición</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $collection->sort_order ?? 0) }}" class="nodo-input">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $collection->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Colección activa
                </label>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('catalog.collections.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
