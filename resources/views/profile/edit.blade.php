<x-layouts.app title="Mi perfil · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Mi perfil' => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Mi perfil</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Actualiza tu información y contraseña.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', auth()->user()->name) }}" required class="nodo-input">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="nodo-input">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Teléfono</label>
                    <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="nodo-input">
                </div>
                <div class="border-t border-slate-100 pt-5 dark:border-slate-800">
                    <label class="nodo-label">Nueva contraseña (opcional)</label>
                    <input type="password" name="password" class="nodo-input">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmation" class="nodo-input">
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="nodo-btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
