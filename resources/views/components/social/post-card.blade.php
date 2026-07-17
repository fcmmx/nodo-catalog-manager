@props(['post'])
<div class="nodo-card overflow-hidden">
    @if ($post->image_path)
        <img src="{{ $post->imageUrl() }}" class="aspect-video w-full object-cover" alt="">
    @endif
    <div class="p-4">
        <div class="mb-2 flex items-center justify-between">
            <span class="nodo-badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $post->channel)) }}</span>
            <span class="nodo-badge {{ match($post->status) {
                'enviada', 'publicada_manual' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
                'error' => 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300',
                'programada' => 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300',
                'pendiente_autorizacion' => 'bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
                'cancelada' => 'bg-slate-200 text-slate-600 dark:bg-slate-700',
                default => 'bg-slate-100 text-slate-600 dark:bg-slate-800',
            } }}">{{ \App\Models\SocialPost::STATUSES[$post->status] ?? $post->status }}</span>
        </div>
        <p class="line-clamp-3 text-sm text-slate-700 dark:text-slate-200">{{ $post->content }}</p>
        @if ($post->scheduled_at)
            <p class="mt-2 text-xs text-slate-400">{{ $post->scheduled_at->format('H:i') }} · {{ $post->account?->label ?? 'Sin cuenta asignada' }}</p>
        @endif
        @if ($post->error_message)
            <p class="mt-2 text-xs text-red-600">{{ $post->error_message }}</p>
        @endif

        <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium">
            @can('editar redes')
                @if ($post->isEditable())
                    <a href="{{ route('social.posts.edit', $post) }}" class="text-blue-600 hover:underline">Editar</a>
                @endif
            @endcan
            @if ($post->image_path)
                <a href="{{ route('social.posts.download', $post) }}" class="text-slate-500 hover:underline">Descargar imagen</a>
            @endif
            @can('crear redes')
                <div x-data="{ open: false }" class="relative inline-block">
                    <button type="button" @click="open = !open" class="text-slate-500 hover:underline">Duplicar…</button>
                    <div x-show="open" x-cloak @click.outside="open = false" class="absolute left-0 top-5 z-10 w-40 nodo-card p-1 shadow-lg">
                        @foreach (\App\Models\SocialPost::CHANNELS as $channel)
                            @if ($channel !== $post->channel)
                                <form method="POST" action="{{ route('social.posts.duplicate', $post) }}">
                                    @csrf
                                    <input type="hidden" name="channel" value="{{ $channel }}">
                                    <button type="submit" class="block w-full rounded px-2 py-1.5 text-left text-xs hover:bg-slate-50 dark:hover:bg-slate-800">{{ ucfirst(str_replace('_',' ',$channel)) }}</button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endcan
            @can('aprobar redes')
                @if ($post->status === 'borrador' || $post->status === 'pendiente_autorizacion')
                    <form method="POST" action="{{ route('social.posts.approve', $post) }}">
                        @csrf
                        <button type="submit" class="text-emerald-600 hover:underline">Aprobar</button>
                    </form>
                @endif
            @endcan
            @can('publicar redes')
                @if (in_array($post->status, ['programada', 'error']) && $post->channel === 'facebook')
                    <form method="POST" action="{{ route('social.posts.publish', $post) }}">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:underline">{{ $post->status === 'error' ? 'Reintentar' : 'Publicar ahora' }}</button>
                    </form>
                @endif
                @if (! in_array($post->status, ['publicada_manual', 'cancelada']))
                    <form method="POST" action="{{ route('social.posts.publish-manual', $post) }}" onsubmit="return confirm('¿Marcar como publicada manualmente?');">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:underline">Publicada manualmente</button>
                    </form>
                @endif
            @endcan
            @can('editar redes')
                @if (! in_array($post->status, ['enviada', 'publicada_manual', 'cancelada']))
                    <form method="POST" action="{{ route('social.posts.cancel', $post) }}">
                        @csrf
                        <button type="submit" class="text-slate-500 hover:underline">Cancelar</button>
                    </form>
                @endif
            @endcan
            @can('eliminar redes')
                <form method="POST" action="{{ route('social.posts.destroy', $post) }}" onsubmit="return confirm('¿Eliminar esta publicación?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</div>
