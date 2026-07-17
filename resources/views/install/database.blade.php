<x-layouts.install title="Base de datos · Instalación" :step="2">
    <h2 class="mb-2 text-lg font-semibold text-slate-900">Conexión a la base de datos</h2>
    <p class="mb-6 text-sm text-slate-500">Ingresa los datos de la base de datos MySQL que creaste en tu hosting (hPanel de Hostinger u otro proveedor).</p>

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.database.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <label class="nodo-label">Host de base de datos</label>
                <input name="host" value="{{ old('host', $old['host']) }}" required class="nodo-input" placeholder="localhost">
            </div>
            <div>
                <label class="nodo-label">Puerto</label>
                <input name="port" value="{{ old('port', $old['port']) }}" required class="nodo-input" placeholder="3306">
            </div>
        </div>
        <div>
            <label class="nodo-label">Nombre de la base de datos</label>
            <input name="database" value="{{ old('database', $old['database']) }}" required class="nodo-input" placeholder="usuario_nododb">
        </div>
        <div>
            <label class="nodo-label">Usuario</label>
            <input name="username" value="{{ old('username', $old['username']) }}" required class="nodo-input" placeholder="usuario_nodo">
        </div>
        <div>
            <label class="nodo-label">Contraseña</label>
            <input type="password" name="password" class="nodo-input">
        </div>

        <div class="flex justify-between pt-2">
            <a href="{{ route('install.welcome') }}" class="nodo-btn-secondary">Atrás</a>
            <button type="submit" class="nodo-btn-primary">Probar conexión y continuar</button>
        </div>
    </form>
</x-layouts.install>
