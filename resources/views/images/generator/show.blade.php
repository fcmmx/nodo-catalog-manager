<x-layouts.app title="Imagen generada · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Imágenes' => route('images.generator'), 'Resultado' => '']">
    <div class="mx-auto max-w-3xl">
        <div class="nodo-card overflow-hidden">
            @if ($generation->status === 'completado' && $generation->file_path)
                <img src="{{ $generation->url() }}" class="w-full" alt="Imagen generada">
            @else
                <div class="flex h-64 items-center justify-center bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300">
                    Error al generar: {{ $generation->error_message }}
                </div>
            @endif

            <div class="p-6">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $generation->template?->name }}</p>
                        <p class="text-xs text-slate-400">Generada {{ $generation->created_at->diffForHumans() }}@if($generation->product) para {{ $generation->product->name }} @endif</p>
                    </div>
                    <span class="nodo-badge {{ $generation->status === 'completado' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300' }}">{{ ucfirst($generation->status) }}</span>
                </div>

                @if ($generation->status === 'completado' && $generation->file_path)
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ $generation->url() }}" download class="nodo-btn-primary">Descargar PNG</a>
                        <a href="{{ route('images.generator', ['product_id' => $generation->product_id, 'template_id' => $generation->template_id]) }}" class="nodo-btn-secondary">Generar otra</a>
                        @can('editar imagenes')
                            @if ($generation->product_id)
                                <form method="POST" action="{{ route('images.generations.use-main', $generation) }}">
                                    @csrf
                                    <button type="submit" class="nodo-btn-secondary">Usar como imagen principal</button>
                                </form>
                                <form method="POST" action="{{ route('images.generations.add-gallery', $generation) }}">
                                    @csrf
                                    <button type="submit" class="nodo-btn-secondary">Agregar a la galería del producto</button>
                                </form>
                            @endif
                        @endcan
                        @can('eliminar imagenes')
                            <form method="POST" action="{{ route('images.generations.destroy', $generation) }}" onsubmit="return confirm('¿Eliminar esta imagen?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="nodo-btn-secondary text-red-600">Eliminar</button>
                            </form>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
