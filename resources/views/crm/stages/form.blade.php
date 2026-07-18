<x-layouts.app :title="($stage->exists ? 'Editar' : 'Nueva').' etapa · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'CRM' => route('crm.index'), 'Etapas' => route('crm.stages.index'), ($stage->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-lg">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $stage->exists ? 'Editar etapa' : 'Nueva etapa' }}</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $stage->exists ? route('crm.stages.update', $stage) : route('crm.stages.store') }}" class="space-y-5">
                @csrf
                @if ($stage->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', $stage->name) }}" required class="nodo-input">
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Color</label>
                        <input type="color" name="color" value="{{ old('color', $stage->color ?? '#2563EB') }}" class="nodo-input h-11">
                    </div>
                    <div>
                        <label class="nodo-label">Orden</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $stage->sort_order ?? 0) }}" class="nodo-input">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="is_won" value="1" {{ old('is_won', $stage->is_won) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Esta etapa representa "ganado"
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="is_lost" value="1" {{ old('is_lost', $stage->is_lost) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Esta etapa representa "perdido"
                    </label>
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('crm.stages.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
