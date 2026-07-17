<x-layouts.install title="Bienvenida · Instalación" :step="1">
    <h2 class="mb-2 text-lg font-semibold text-slate-900">Bienvenido al asistente de instalación</h2>
    <p class="mb-6 text-sm text-slate-500">Antes de continuar, verificamos que el servidor cumpla los requisitos necesarios para ejecutar NODO Catalog Manager.</p>

    <div class="space-y-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Versión de PHP</p>
            <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <span>{{ $php['label'] }} — {{ $php['detail'] }}</span>
                <x-install.status-icon :ok="$php['ok']" />
            </div>
        </div>

        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Extensiones de PHP</p>
            <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                @foreach ($extensions as $ext)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <span>{{ $ext['label'] }}</span>
                        <x-install.status-icon :ok="$ext['ok']" />
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Permisos de carpetas</p>
            <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
                @foreach ($permissions as $perm)
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <span>{{ $perm['label'] }}</span>
                        <x-install.status-icon :ok="$perm['ok']" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-8 flex items-center justify-between">
        @if (!$allPassed)
            <p class="text-sm text-red-600">Corrige los puntos marcados en rojo antes de continuar.</p>
        @else
            <p class="text-sm text-emerald-600">Todos los requisitos se cumplen correctamente.</p>
        @endif
    </div>

    <div class="mt-4 flex justify-end gap-3">
        <a href="{{ route('install.welcome') }}" class="nodo-btn-secondary">Volver a verificar</a>
        @if ($allPassed)
            <a href="{{ route('install.database.form') }}" class="nodo-btn-primary">Continuar</a>
        @else
            <span class="nodo-btn-primary cursor-not-allowed opacity-50">Continuar</span>
        @endif
    </div>
</x-layouts.install>
