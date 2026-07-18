<x-layouts.app title="Auditor SEO/AEO/GEO · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Auditor IA-Ready' => '']">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Auditor SEO / AEO / GEO</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Analiza cualquier sitio web (propio o de un cliente) y obtén una calificación de 0 a 100 sobre qué tan preparado está para buscadores tradicionales y para motores de respuesta por IA (ChatGPT, Perplexity, Google AI Overviews, etc.).</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
    @endif

    @can('crear auditoria')
        <div class="nodo-card mb-6 p-6" x-data="{ loading: false }">
            <h2 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Nueva auditoría</h2>
            <form method="POST" action="{{ route('seo-audit.store') }}" @submit="loading = true" class="flex flex-wrap gap-2">
                @csrf
                <input type="url" name="url" required placeholder="https://ejemplo.com" value="{{ old('url') }}" class="nodo-input flex-1 min-w-[240px]">
                <button type="submit" class="nodo-btn-primary shrink-0" :disabled="loading">
                    <span x-show="!loading">Analizar sitio</span>
                    <span x-show="loading" x-cloak>Analizando…</span>
                </button>
            </form>
            @error('url') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-2 text-xs text-slate-400">El análisis descarga el HTML público de la URL en tiempo real (puede tardar unos segundos según la velocidad del sitio).</p>
        </div>
    @endcan

    @if ($audits->isEmpty())
        <x-ui.empty-state title="Todavía no se ha auditado ningún sitio" description="Analiza una URL para ver su calificación de SEO, AEO y GEO." />
    @else
        <div class="nodo-card overflow-hidden">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500 dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Calificación</th>
                        <th class="px-4 py-3">SEO</th>
                        <th class="px-4 py-3">AEO</th>
                        <th class="px-4 py-3">GEO</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($audits as $audit)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                                <a href="{{ route('seo-audit.show', $audit) }}" class="hover:underline">{{ \Illuminate\Support\Str::limit($audit->url, 45) }}</a>
                            </td>
                            <td class="px-4 py-3">
                                @if ($audit->status === 'error')
                                    <span class="nodo-badge bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300">Error</span>
                                @else
                                    <span class="nodo-badge {{ $audit->score >= 75 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($audit->score >= 40 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300') }}">{{ $audit->score }}/100 ({{ $audit->grade() }})</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $audit->seo_score ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $audit->aeo_score ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $audit->geo_score ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2 text-xs font-medium">
                                    <a href="{{ route('seo-audit.show', $audit) }}" class="text-blue-600 hover:underline">Ver</a>
                                    @can('crear auditoria')
                                        <form method="POST" action="{{ route('seo-audit.destroy', $audit) }}" onsubmit="return confirm('¿Eliminar esta auditoría?');">
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
        <div class="mt-4">{{ $audits->links() }}</div>
    @endif
</x-layouts.app>
