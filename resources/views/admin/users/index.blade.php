<x-layouts.app title="Usuarios y roles · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Usuarios y roles' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Usuarios y roles</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Administra quién puede acceder al sistema y qué puede hacer.</p>
        </div>
        @can('administrar usuarios')
            <a href="{{ route('admin.users.create') }}" class="nodo-btn-primary">+ Nuevo usuario</a>
        @endcan
    </div>

    <form method="GET" class="mb-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo…" class="nodo-input max-w-xs">
    </form>

    <div class="nodo-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3">Usuario</th>
                    <th class="px-4 py-3">Roles</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Último acceso</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @foreach ($user->roles as $role)
                                <span class="nodo-badge mr-1 mb-1 bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">
                            <span class="nodo-badge {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800' }}">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $user->last_login_at?->diffForHumans() ?? 'Nunca' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2 text-xs font-medium">
                                @can('administrar usuarios')
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:underline">Editar</a>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts.app>
