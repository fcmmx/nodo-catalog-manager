<x-layouts.app :title="($product->exists ? 'Editar' : 'Nuevo').' producto · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Catálogo' => route('catalog.products.index'), ($product->exists ? $product->name : 'Nuevo producto') => '']">
    <form method="POST" action="{{ $product->exists ? route('catalog.products.update', $product) : route('catalog.products.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($product->exists) @method('PUT') @endif

        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $product->exists ? 'Editar producto' : 'Nuevo producto' }}</h1>
            <div class="flex gap-2">
                @if ($product->exists)
                    <a href="{{ route('catalog.products.preview', $product) }}" target="_blank" class="nodo-btn-secondary">Vista previa</a>
                @endif
                <a href="{{ route('catalog.products.index') }}" class="nodo-btn-secondary">Cancelar</a>
                <button type="submit" class="nodo-btn-primary">Guardar</button>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Información general</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">SKU / Código</label>
                            <input name="sku" value="{{ old('sku', $product->sku) }}" required class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Tipo</label>
                            <select name="type" class="nodo-input">
                                <option value="servicio" {{ old('type', $product->type) === 'servicio' ? 'selected' : '' }}>Servicio</option>
                                <option value="producto" {{ old('type', $product->type) === 'producto' ? 'selected' : '' }}>Producto</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Nombre</label>
                        <input name="name" value="{{ old('name', $product->name) }}" required class="nodo-input">
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Nombre corto</label>
                            <input name="short_name" value="{{ old('short_name', $product->short_name) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Slug (URL amigable)</label>
                            <input name="slug" value="{{ old('slug', $product->slug) }}" class="nodo-input" placeholder="se genera automáticamente">
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Colección</label>
                            <select name="collection_id" class="nodo-input">
                                <option value="">Sin colección</option>
                                @foreach ($collections as $c)
                                    <option value="{{ $c->id }}" {{ old('collection_id', $product->collection_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="nodo-label">Categoría</label>
                            <select name="category_id" class="nodo-input">
                                <option value="">Sin categoría</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $product->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="nodo-card p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Contenido</h2>
                        @can('usar ia')
                            <span class="text-xs text-slate-400">Usa los enlaces "Generar con IA" junto a cada campo para redactar con ayuda de inteligencia artificial.</span>
                        @endcan
                    </div>
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="short_description" class="nodo-label mb-0">Descripción corta</label>
                            @can('usar ia')
                                <x-ai.assist-button target="short_description" task="descripcion_corta" :tema="old('name', $product->name)" />
                            @endcan
                        </div>
                        <textarea name="short_description" id="short_description" rows="2" class="nodo-input">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>
                    <div class="mt-5">
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="description" class="nodo-label mb-0">Descripción completa</label>
                            @can('usar ia')
                                <x-ai.assist-button target="description" task="descripcion_completa" :tema="old('name', $product->name)" />
                            @endcan
                        </div>
                        <textarea name="description" id="description" rows="6" class="nodo-input">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="benefits" class="nodo-label mb-0">Beneficios (uno por línea)</label>
                                @can('usar ia')
                                    <x-ai.assist-button target="benefits" task="beneficios" :tema="old('name', $product->name)" />
                                @endcan
                            </div>
                            <textarea name="benefits" id="benefits" rows="5" class="nodo-input">{{ old('benefits', $product->benefits) }}</textarea>
                        </div>
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="features" class="nodo-label mb-0">Características (una por línea)</label>
                                @can('usar ia')
                                    <x-ai.assist-button target="features" task="caracteristicas" :tema="old('name', $product->name)" />
                                @endcan
                            </div>
                            <textarea name="features" id="features" rows="5" class="nodo-input">{{ old('features', $product->features) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Precio</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="nodo-label">Precio</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Precio anterior</label>
                            <input type="number" step="0.01" name="old_price" value="{{ old('old_price', $product->old_price) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Moneda</label>
                            <select name="currency" class="nodo-input">
                                <option value="MXN" {{ old('currency', $product->currency ?? 'MXN') === 'MXN' ? 'selected' : '' }}>MXN</option>
                                <option value="USD" {{ old('currency', $product->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                            </select>
                        </div>
                        <div>
                            <label class="nodo-label">Modalidad de cobro</label>
                            <input name="pricing_model" value="{{ old('pricing_model', $product->pricing_model) }}" class="nodo-input" placeholder="mensual, proyecto…">
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Texto "desde"</label>
                            <input name="price_prefix_text" value="{{ old('price_prefix_text', $product->price_prefix_text) }}" class="nodo-input" placeholder="Desde">
                        </div>
                        <label class="mt-6 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                            <input type="checkbox" name="tax_included" value="1" {{ old('tax_included', $product->tax_included) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                            IVA incluido en el precio
                        </label>
                    </div>
                </div>

                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Enlaces</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">URL</label>
                            <input name="url" value="{{ old('url', $product->url) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">URL de demostración</label>
                            <input name="demo_url" value="{{ old('demo_url', $product->demo_url) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Video (URL)</label>
                            <input name="video_url" value="{{ old('video_url', $product->video_url) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">URL de WhatsApp</label>
                            <input name="whatsapp_url" value="{{ old('whatsapp_url', $product->whatsapp_url) }}" class="nodo-input">
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="mb-1.5 flex items-center justify-between">
                            <label for="whatsapp_message" class="nodo-label mb-0">Mensaje predeterminado de WhatsApp</label>
                            @can('usar ia')
                                <x-ai.assist-button target="whatsapp_message" task="mensaje_whatsapp" :tema="old('name', $product->name)" />
                            @endcan
                        </div>
                        <textarea name="whatsapp_message" id="whatsapp_message" rows="2" class="nodo-input">{{ old('whatsapp_message', $product->whatsapp_message) }}</textarea>
                    </div>
                </div>

                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">SEO</h2>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Meta título</label>
                            <input name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" class="nodo-input">
                        </div>
                        <div>
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="keywords" class="nodo-label mb-0">Palabras clave</label>
                                @can('usar ia')
                                    <x-ai.assist-button target="keywords" task="palabras_clave" :tema="old('name', $product->name)" />
                                @endcan
                            </div>
                            <input name="keywords" id="keywords" value="{{ old('keywords', $product->keywords) }}" class="nodo-input">
                        </div>
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Meta descripción</label>
                        <textarea name="meta_description" rows="2" class="nodo-input">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Texto SEO adicional</label>
                        <textarea name="seo_text" rows="2" class="nodo-input">{{ old('seo_text', $product->seo_text) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Publicación</h2>
                    <div>
                        <label class="nodo-label">Estado</label>
                        <select name="status" class="nodo-input">
                            @foreach (\App\Models\Product::STATUSES as $s)
                                <option value="{{ $s }}" {{ old('status', $product->status ?? 'borrador') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Disponibilidad</label>
                        <select name="availability" class="nodo-input">
                            @foreach (\App\Models\Product::AVAILABILITIES as $a)
                                <option value="{{ $a }}" {{ old('availability', $product->availability ?? 'disponible') === $a ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$a)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Fecha de publicación</label>
                        <input type="datetime-local" name="published_at" value="{{ old('published_at', $product->published_at?->format('Y-m-d\TH:i')) }}" class="nodo-input">
                    </div>
                    <div class="mt-5">
                        <label class="nodo-label">Orden de aparición</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="nodo-input">
                    </div>
                    <label class="mt-5 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Producto destacado
                    </label>
                    <div class="mt-5">
                        <label class="nodo-label">Etiquetas (separadas por coma)</label>
                        <input name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" class="nodo-input">
                    </div>
                </div>

                <div class="nodo-card p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Imagen principal</h2>
                        @if ($product->exists)
                            @can('ver imagenes')
                                <a href="{{ route('images.generator', ['product_id' => $product->id]) }}" class="text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">Generar imagen comercial</a>
                            @endcan
                        @endif
                    </div>
                    @if ($product->main_image)
                        <img src="{{ $product->imageUrl() }}" class="mb-3 aspect-video w-full rounded-lg object-cover" alt="{{ $product->name }}">
                    @endif
                    <input type="file" name="main_image" accept="image/png,image/jpeg,image/webp" class="nodo-input">

                    @can('usar ia')
                        <div class="mt-4 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <div class="mb-1.5 flex items-center justify-between">
                                <label for="ai_image_prompt_output" class="nodo-label mb-0">Prompt para generador de imágenes</label>
                                <x-ai.assist-button target="ai_image_prompt_output" task="prompt_imagen" :tema="old('name', $product->name)" label="Generar prompt con IA" />
                            </div>
                            <textarea id="ai_image_prompt_output" rows="3" class="nodo-input text-xs" placeholder="Genera un prompt en inglés listo para pegar en tu herramienta de generación de imágenes preferida." readonly></textarea>
                            <p class="mt-1 text-xs text-slate-400">Copia este prompt y pégalo en "Generar imagen comercial" (arriba) al elegir el origen de fondo "Generar con IA".</p>
                        </div>
                    @endcan

                    <h2 class="mb-2 mt-6 text-sm font-semibold text-slate-900 dark:text-white">Galería</h2>
                    @if ($product->exists && $product->images->isNotEmpty())
                        <div class="mb-3 grid grid-cols-3 gap-2">
                            @foreach ($product->images as $img)
                                <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" class="aspect-square rounded-lg object-cover">
                            @endforeach
                        </div>
                    @endif
                    <input type="file" name="gallery[]" multiple accept="image/png,image/jpeg,image/webp" class="nodo-input">
                </div>

                @if ($product->exists)
                    <div class="nodo-card p-6 text-xs text-slate-400">
                        <p>Creado: {{ $product->created_at->format('d/m/Y H:i') }}</p>
                        <p>Actualizado: {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                        @if ($product->creator) <p>Por: {{ $product->creator->name }}</p> @endif
                    </div>
                @endif
            </div>
        </div>
    </form>
</x-layouts.app>
