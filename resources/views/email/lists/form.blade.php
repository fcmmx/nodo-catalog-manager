<x-layouts.app :title="($list->exists ? 'Editar' : 'Nueva').' lista · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Listas' => route('email.lists.index'), ($list->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-lg">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $list->exists ? 'Editar lista' : 'Nueva lista' }}</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $list->exists ? route('email.lists.update', $list) : route('email.lists.store') }}" class="space-y-5">
                @csrf
                @if ($list->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', $list->name) }}" required class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">Descripción</label>
                    <textarea name="description" rows="3" class="nodo-input">{{ old('description', $list->description) }}</textarea>
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('email.lists.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
