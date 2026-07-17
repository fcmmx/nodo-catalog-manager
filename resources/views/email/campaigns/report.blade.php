<x-layouts.app :title="'Reporte · '.$campaign->name.' · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), $campaign->name => '']">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->name }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ \App\Models\EmailCampaign::TYPES[$campaign->type] ?? $campaign->type }} — {{ \App\Models\EmailCampaign::STATUSES[$campaign->status] ?? $campaign->status }}</p>
    </div>

    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Enviados</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->sent_count }}</p>
        </div>
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Aperturas</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->open_count }} <span class="text-sm font-normal text-slate-400">({{ $campaign->openRate() }}%)</span></p>
        </div>
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Clics</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->click_count }} <span class="text-sm font-normal text-slate-400">({{ $campaign->clickRate() }}%)</span></p>
        </div>
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Rebotes</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->bounce_count }}</p>
        </div>
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Bajas</p>
            <p class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->unsubscribe_count }}</p>
        </div>
        <div class="nodo-card p-4">
            <p class="text-xs uppercase tracking-wide text-slate-400">Enviada el</p>
            <p class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">{{ $campaign->sent_at?->format('d/m/Y H:i') ?? '—' }}</p>
        </div>
    </div>

    @if ($sends->isEmpty())
        <x-ui.empty-state title="Todavía no hay envíos registrados" description="Los envíos aparecerán aquí conforme la campaña se procese." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Contacto</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Enviado</th>
                        <th class="px-4 py-3">Abierto</th>
                        <th class="px-4 py-3">Clic</th>
                        <th class="px-4 py-3">Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($sends as $send)
                        <tr>
                            <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $send->contact->email ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="nodo-badge {{ match($send->status) { 'enviado' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300', 'error' => 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300', default => 'bg-slate-100 text-slate-600 dark:bg-slate-800' } }}">{{ ucfirst($send->status) }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $send->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $send->opened_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $send->clicked_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-red-600">{{ $send->error_message ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sends->links() }}</div>
    @endif

    <div class="mt-6">
        <a href="{{ route('email.campaigns.index') }}" class="nodo-btn-secondary">← Volver a campañas</a>
    </div>
</x-layouts.app>
