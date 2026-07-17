<x-layouts.app title="Cuentas conectadas · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Redes Sociales' => route('social.posts.index'), 'Cuentas' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Cuentas conectadas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Conecta las cuentas de tus redes sociales para publicar directamente. Sin credenciales, puedes seguir preparando contenido y publicarlo manualmente.</p>
        </div>
        @can('conectar cuentas redes')
            <a href="{{ route('social.accounts.create') }}" class="nodo-btn-primary">+ Conectar cuenta</a>
        @endcan
    </div>

    @if ($accounts->isEmpty())
        <x-ui.empty-state title="Todavía no has conectado ninguna cuenta" description="Puedes seguir creando y programando publicaciones; para enviarlas automáticamente necesitarás conectar la cuenta correspondiente." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Canal</th>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Publicaciones</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($accounts as $account)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $account->channel)) }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $account->label }}</td>
                            <td class="px-4 py-3">
                                @if ($account->isAuthorized())
                                    <span class="nodo-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Autorizada</span>
                                @else
                                    <span class="nodo-badge bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300">Sin token / expirado</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $account->posts_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    @can('conectar cuentas redes')
                                        <a href="{{ route('social.accounts.edit', $account) }}" class="text-blue-600 hover:underline">Editar</a>
                                        <form method="POST" action="{{ route('social.accounts.destroy', $account) }}" onsubmit="return confirm('¿Desconectar esta cuenta?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Desconectar</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6 rounded-xl bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:bg-blue-950 dark:text-blue-300">
        Por ahora, la publicación automática real está implementada para <strong>Facebook</strong> (Graph API de Meta). Las demás redes (Instagram, LinkedIn, TikTok, X, Google Business Profile) permiten preparar, programar y descargar el contenido para publicarlo manualmente hasta que se conecte su conector específico.
    </div>
</x-layouts.app>
