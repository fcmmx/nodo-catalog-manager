<x-layouts.guest title="Recuperar contraseña · NODO Catalog Manager">
    <h2 class="mb-2 text-lg font-semibold text-slate-900 dark:text-white">Recupera tu contraseña</h2>
    <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Escribe tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="nodo-label">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="nodo-input">
        </div>
        <button type="submit" class="nodo-btn-primary w-full">Enviar enlace de recuperación</button>
        <a href="{{ route('login') }}" class="block text-center text-sm text-slate-500 hover:underline dark:text-slate-400">Volver a iniciar sesión</a>
    </form>
</x-layouts.guest>
