<x-layouts.app :title="'Prospectos · '.$landing->name.' · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Landing Pages' => route('landing.index'), $landing->name => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $landing->name }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $landing->views_count }} vistas — {{ $landing->leads_count }} prospectos — {{ $landing->conversionRate() }}% de conversión</p>
        </div>
        <div class="flex gap-2">
            @if ($landing->isPublished())
                <a href="{{ $landing->publicUrl() }}" target="_blank" class="nodo-btn-secondary">Ver landing ↗</a>
            @endif
            <a href="{{ route('landing.qr', $landing) }}" class="nodo-btn-secondary">Descargar QR</a>
            <a href="{{ route('landing.edit', $landing) }}" class="nodo-btn-primary">Editar</a>
        </div>
    </div>

    @if ($leads->isEmpty())
        <x-ui.empty-state title="Todavía no hay prospectos" description="Cuando alguien complete el formulario de la landing page, aparecerá aquí." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Mensaje</th>
                        <th class="px-4 py-3">Origen (UTM)</th>
                        <th class="px-4 py-3">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($leads as $lead)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">{{ $lead->name }}</td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $lead->email }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->phone ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->message ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->utm_source ?: '—' }}{{ $lead->utm_campaign ? ' / '.$lead->utm_campaign : '' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $leads->links() }}</div>
    @endif
</x-layouts.app>
