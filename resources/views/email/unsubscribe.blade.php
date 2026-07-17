<x-layouts.guest title="Baja de correos · NODO Catalog Manager">
    <h2 class="mb-2 text-center text-lg font-semibold text-slate-900 dark:text-white">Darse de baja</h2>
    <p class="mb-6 text-center text-sm text-slate-500 dark:text-slate-400">
        {{ $contact->email }} — ¿Confirmas que ya no deseas recibir correos de NODO 360?
    </p>

    <form method="POST" action="{{ route('email.unsubscribe', $token) }}" class="space-y-3">
        @csrf
        <button type="submit" class="nodo-btn-primary w-full">Sí, darme de baja</button>
    </form>
</x-layouts.guest>
