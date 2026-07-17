<x-layouts.app title="Campañas de email · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Campañas de email marketing</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $campaigns->total() }} campaña(s)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('email.contacts.index') }}" class="nodo-btn-secondary">Contactos</a>
            <a href="{{ route('email.lists.index') }}" class="nodo-btn-secondary">Listas</a>
            @can('configurar campanas')
                <a href="{{ route('admin.email.settings.edit') }}" class="nodo-btn-secondary">Configuración</a>
            @endcan
            @can('crear campanas')
                <a href="{{ route('email.campaigns.create') }}" class="nodo-btn-primary">+ Nueva campaña</a>
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
        <select name="status" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    @if ($campaigns->isEmpty())
        <x-ui.empty-state title="No hay campañas todavía" description="Crea una campaña, arma su contenido con el constructor de bloques y envíala a una de tus listas." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Campaña</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Lista</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Enviados</th>
                        <th class="px-4 py-3">Apertura</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($campaigns as $campaign)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $campaign->name }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ \App\Models\EmailCampaign::TYPES[$campaign->type] ?? $campaign->type }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $campaign->list?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ match($campaign->status) { 'enviada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'enviando', 'programada' => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300', 'pausada' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300', default => 'bg-slate-100 text-slate-600 dark:bg-slate-800' } }}">{{ $statuses[$campaign->status] ?? $campaign->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $campaign->sent_count }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $campaign->openRate() }}%</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    <a href="{{ route('email.campaigns.report', $campaign) }}" class="text-slate-500 hover:underline">Reporte</a>
                                    @can('editar campanas')
                                        @if (in_array($campaign->status, ['borrador', 'programada', 'pausada']))
                                            <a href="{{ route('email.campaigns.edit', $campaign) }}" class="text-blue-600 hover:underline">Editar</a>
                                        @endif
                                    @endcan
                                    @can('eliminar campanas')
                                        <form method="POST" action="{{ route('email.campaigns.destroy', $campaign) }}" onsubmit="return confirm('¿Eliminar esta campaña?');">
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
        <div class="mt-4">{{ $campaigns->links() }}</div>
    @endif
</x-layouts.app>
