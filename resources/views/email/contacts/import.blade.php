<x-layouts.app title="Importar contactos · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Contactos' => route('email.contacts.index'), 'Importar' => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Importar contactos</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Sube un archivo CSV, XLSX o XLS con una columna <strong>email</strong> (obligatoria) y opcionalmente <strong>name</strong>/<strong>nombre</strong> y <strong>phone</strong>/<strong>telefono</strong>. Los contactos importados quedan con consentimiento registrado.</p>

            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('email.contacts.import') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="nodo-label">Archivo</label>
                    <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" required class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">Agregar a la lista (opcional)</label>
                    <select name="list_id" class="nodo-input">
                        <option value="">Sin lista</option>
                        @foreach ($lists as $list)
                            <option value="{{ $list->id }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-between pt-2">
                    <a href="{{ route('email.contacts.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
