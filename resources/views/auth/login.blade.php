<x-layouts.guest title="Iniciar sesión · NODO Catalog Manager">
    <h2 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">Inicia sesión en tu cuenta</h2>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="nodo-label">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="nodo-input" placeholder="tucorreo@empresa.com">
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="nodo-label mb-0">Contraseña</label>
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">¿Olvidaste tu contraseña?</a>
            </div>
            <input id="password" type="password" name="password" required class="nodo-input" placeholder="••••••••">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
            Mantener sesión iniciada
        </label>

        <button type="submit" class="nodo-btn-primary w-full">Iniciar sesión</button>
    </form>
</x-layouts.guest>
