<x-layouts.install title="Instalación completa" :step="5">
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
            <svg class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
        </div>
        <h2 class="mb-2 text-lg font-semibold text-slate-900">¡Instalación completada!</h2>
        <p class="mb-6 text-sm text-slate-500">NODO Catalog Manager quedó instalado y el asistente se bloqueó automáticamente por seguridad.</p>

        @if ($email)
            <div class="mb-6 rounded-lg bg-slate-50 px-4 py-3 text-left text-sm">
                <p class="text-slate-500">Usuario administrador:</p>
                <p class="font-medium text-slate-900">{{ $email }}</p>
            </div>
        @endif

        <a href="{{ route('login') }}" class="nodo-btn-primary">Iniciar sesión</a>

        <p class="mt-6 text-xs text-slate-400">Por seguridad, elimina o restringe el acceso a esta URL de instalación desde tu servidor si tu proveedor no lo hace automáticamente.</p>
    </div>
</x-layouts.install>
