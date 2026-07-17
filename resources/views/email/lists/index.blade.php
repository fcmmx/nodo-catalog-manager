<x-layouts.app title="Listas de contactos · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Listas' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Listas de contactos</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Agrupa tus contactos para segmentar campañas.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('email.contacts.index') }}" class="nodo-btn-secondary">Contactos</a>
            @can('crear contactos')
                <a href="{{ route('email.lists.create') }}" class="nodo-btn-primary">+ Nueva lista</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif

    @if ($lists->isEmpty())
        <x-ui.empty-state title="Todavía no has creado ninguna lista" description="Crea una lista para organizar y segmentar tus contactos antes de enviar campañas." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">Contactos</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($lists as $list)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $list->name }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $list->description ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                <a href="{{ route('email.contacts.index', ['list_id' => $list->id]) }}" class="hover:underline">{{ $list->contacts_count }}</a>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    @can('editar contactos')
                                        <a href="{{ route('email.lists.edit', $list) }}" class="text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('eliminar contactos')
                                        <form method="POST" action="{{ route('email.lists.destroy', $list) }}" onsubmit="return confirm('¿Eliminar esta lista? Los contactos no se eliminarán.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
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
</x-layouts.app>
