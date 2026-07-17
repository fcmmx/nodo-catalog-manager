<x-layouts.guest title="Restablecer contraseña · NODO Catalog Manager">
    <h2 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">Restablece tu contraseña</h2>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label for="email" class="nodo-label">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus class="nodo-input">
        </div>
        <div>
            <label for="password" class="nodo-label">Nueva contraseña</label>
            <input id="password" type="password" name="password" required class="nodo-input">
        </div>
        <div>
            <label for="password_confirmation" class="nodo-label">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="nodo-input">
        </div>
        <button type="submit" class="nodo-btn-primary w-full">Restablecer contraseña</button>
    </form>
</x-layouts.guest>
