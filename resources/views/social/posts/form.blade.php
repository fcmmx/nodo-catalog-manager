<x-layouts.app :title="($post->exists ? 'Editar' : 'Nueva').' publicación · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Redes Sociales' => route('social.posts.index'), ($post->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-2xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $post->exists ? 'Editar publicación' : 'Nueva publicación' }}</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $post->exists ? route('social.posts.update', $post) : route('social.posts.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if ($post->exists) @method('PUT') @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Canal</label>
                        <select name="channel" class="nodo-input">
                            @foreach (\App\Models\SocialPost::CHANNELS as $channel)
                                <option value="{{ $channel }}" {{ old('channel', $post->channel) === $channel ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="nodo-label">Cuenta (opcional)</label>
                        <select name="social_account_id" class="nodo-input">
                            <option value="">Sin asignar</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('social_account_id', $post->social_account_id) == $account->id ? 'selected' : '' }}>{{ $account->label }} ({{ $account->channel }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="nodo-label">Producto relacionado (opcional)</label>
                    <select name="product_id" class="nodo-input">
                        <option value="">Sin producto</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $post->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="nodo-label">Contenido</label>
                    <textarea name="content" rows="5" class="nodo-input" required>{{ old('content', $post->content ?? $selectedProduct?->short_description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Hashtags</label>
                        <input name="hashtags" value="{{ old('hashtags', $post->hashtags) }}" class="nodo-input" placeholder="#NODO360 #IA">
                    </div>
                    <div>
                        <label class="nodo-label">Enlace</label>
                        <input name="link" value="{{ old('link', $post->link ?? $selectedProduct?->url) }}" class="nodo-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Fecha y hora de publicación (opcional)</label>
                        <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', $post->scheduled_at?->format('Y-m-d\TH:i')) }}" class="nodo-input">
                        <p class="mt-1 text-xs text-slate-400">Déjalo vacío para guardar como borrador.</p>
                    </div>
                    <div>
                        <label class="nodo-label">Zona horaria</label>
                        <input name="timezone" value="{{ old('timezone', $post->timezone ?? 'America/Mexico_City') }}" class="nodo-input">
                    </div>
                </div>

                <div>
                    <label class="nodo-label">Imagen</label>
                    @if ($post->image_path)
                        <img src="{{ $post->imageUrl() }}" class="mb-2 aspect-video w-full max-w-xs rounded-lg object-cover">
                    @endif
                    <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="nodo-input">
                    @can('ver imagenes')
                        <p class="mt-1 text-xs text-slate-400">¿Necesitas crear una imagen? Usa el <a href="{{ route('images.generator', ['product_id' => $post->product_id]) }}" class="text-blue-600 hover:underline">generador de imágenes</a>, descárgala y súbela aquí.</p>
                    @endcan
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('social.posts.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
