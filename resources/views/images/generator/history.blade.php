<x-layouts.app title="Historial de imágenes · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Imágenes' => route('images.generator'), 'Historial' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Historial de imágenes generadas</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $generations->total() }} imagen(es)</p>
        </div>
        <a href="{{ route('images.generator') }}" class="nodo-btn-primary">+ Nueva imagen</a>
    </div>

    @if ($generations->isEmpty())
        <x-ui.empty-state title="Todavía no has generado ninguna imagen" description="Usa el generador para crear tu primera imagen comercial." />
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($generations as $generation)
                <a href="{{ route('images.generations.show', $generation) }}" class="nodo-card overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
                    @if ($generation->status === 'completado' && $generation->file_path)
                        <img src="{{ $generation->url() }}" class="aspect-square w-full object-cover" alt="{{ $generation->title }}">
                    @else
                        <div class="flex aspect-square items-center justify-center bg-red-50 text-xs text-red-600 dark:bg-red-950 dark:text-red-300">Error</div>
                    @endif
                    <div class="p-3">
                        <p class="truncate text-xs font-medium text-slate-700 dark:text-slate-200">{{ $generation->title ?: $generation->template?->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ $generation->created_at->diffForHumans() }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $generations->links() }}</div>
    @endif
</x-layouts.app>
