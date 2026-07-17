<x-layouts.app :title="($user->exists ? 'Editar' : 'Nuevo').' usuario · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Usuarios y roles' => route('admin.users.index'), ($user->exists ? 'Editar' : 'Nuevo') => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $user->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>

            <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" class="space-y-5">
                @csrf
                @if ($user->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre completo</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="nodo-input">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="nodo-input">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Teléfono</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">{{ $user->exists ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label>
                    <input type="password" name="password" {{ $user->exists ? '' : 'required' }} class="nodo-input">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="nodo-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="nodo-input">
                </div>

                <div>
                    <label class="nodo-label">Roles</label>
                    <div class="grid grid-cols-2 gap-2 rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                        @foreach ($roles as $role)
                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                                {{ $role->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Usuario activo
                </label>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('admin.users.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
