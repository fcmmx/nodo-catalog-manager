<x-layouts.app title="Contactos · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Contactos' => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Contactos</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $contacts->total() }} contacto(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('email.lists.index') }}" class="nodo-btn-secondary">Listas</a>
            @can('exportar contactos')
                <a href="{{ route('email.contacts.export', request()->query()) }}" class="nodo-btn-secondary">Exportar CSV</a>
            @endcan
            @can('importar contactos')
                <a href="{{ route('email.contacts.import.form') }}" class="nodo-btn-secondary">Importar</a>
            @endcan
            @can('crear contactos')
                <a href="{{ route('email.contacts.create') }}" class="nodo-btn-primary">+ Nuevo contacto</a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre o correo…" class="nodo-input w-64">
        <select name="list_id" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todas las listas</option>
            @foreach ($lists as $list)
                <option value="{{ $list->id }}" {{ (string) request('list_id') === (string) $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
            @endforeach
        </select>
        <select name="subscribed" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="0" {{ request('subscribed') === '0' ? 'selected' : '' }}>Solo dados de baja</option>
        </select>
        <button type="submit" class="nodo-btn-secondary">Buscar</button>
    </form>

    @if ($contacts->isEmpty())
        <x-ui.empty-state title="No hay contactos" description="Agrega contactos manualmente o impórtalos desde un archivo CSV/Excel." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Listas</th>
                        <th class="px-4 py-3">Consentimiento</th>
                        <th class="px-4 py-3">Suscrito</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($contacts as $contact)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $contact->name ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $contact->email }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $contact->lists->pluck('name')->join(', ') ?: '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($contact->consent)
                                    <span class="nodo-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Sí</span>
                                @else
                                    <span class="nodo-badge bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($contact->subscribed)
                                    <span class="nodo-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Suscrito</span>
                                @else
                                    <span class="nodo-badge bg-slate-100 text-slate-600 dark:bg-slate-800">De baja</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    @can('editar contactos')
                                        <a href="{{ route('email.contacts.edit', $contact) }}" class="text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @can('eliminar contactos')
                                        <form method="POST" action="{{ route('email.contacts.destroy', $contact) }}" onsubmit="return confirm('¿Eliminar este contacto?');">
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
        <div class="mt-4">{{ $contacts->links() }}</div>
    @endif
</x-layouts.app>
