<x-layouts.app :title="($category->exists ? 'Editar' : 'Nueva').' categoría · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Categorías' => route('catalog.categories.index'), ($category->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $category->exists ? 'Editar categoría' : 'Nueva categoría' }}</h1>

            <form method="POST" action="{{ $category->exists ? route('catalog.categories.update', $category) : route('catalog.categories.store') }}" class="space-y-5">
                @csrf
                @if ($category->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', $category->name) }}" required class="nodo-input">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Colección (opcional)</label>
                    <select name="collection_id" class="nodo-input">
                        <option value="">Sin colección</option>
                        @foreach ($collections as $c)
                            <option value="{{ $c->id }}" {{ old('collection_id', $category->collection_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="nodo-label">Descripción</label>
                    <textarea name="description" rows="3" class="nodo-input">{{ old('description', $category->description) }}</textarea>
                </div>
                <div>
                    <label class="nodo-label">Orden de aparición</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="nodo-input">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Categoría activa
                </label>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('catalog.categories.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
