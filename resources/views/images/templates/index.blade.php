<x-layouts.app title="Plantillas de imagen · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Imágenes' => route('images.generator'), 'Plantillas' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Plantillas de imagen</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Formatos y estilos reutilizables para el generador de imágenes.</p>
        </div>
        @can('crear imagenes')
            <a href="{{ route('images.templates.create') }}" class="nodo-btn-primary">+ Nueva plantilla</a>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($templates as $template)
            <div class="nodo-card overflow-hidden">
                <div class="flex h-32 items-center justify-center" style="background: linear-gradient(180deg, {{ $template->background_type === 'color' ? $template->background_value : '#0F172A' }}, {{ $template->primary_color }})">
                    <span class="text-xs font-medium text-white/80">{{ \App\Models\ImageTemplate::FORMATS[$template->format]['label'] ?? $template->format }}</span>
                </div>
                <div class="p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <h3 class="font-medium text-slate-900 dark:text-white">{{ $template->name }}</h3>
                        @if ($template->is_master)
                            <span class="nodo-badge bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300">Maestra</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">{{ $template->width }}×{{ $template->height }} · {{ $template->generations_count }} imagen(es) generada(s)</p>
                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('images.generator', ['template_id' => $template->id]) }}" class="nodo-btn-secondary flex-1 text-xs">Usar</a>
                        @can('editar imagenes')
                            <a href="{{ route('images.templates.edit', $template) }}" class="nodo-btn-secondary text-xs">Editar</a>
                        @endcan
                        @can('eliminar imagenes')
                            @unless ($template->is_master)
                                <form method="POST" action="{{ route('images.templates.destroy', $template) }}" onsubmit="return confirm('¿Eliminar esta plantilla?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="nodo-btn-secondary text-xs text-red-600">Eliminar</button>
                                </form>
                            @endunless
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
