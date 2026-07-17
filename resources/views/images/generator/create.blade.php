<x-layouts.app title="Generador de imágenes · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Imágenes' => route('images.generator'), 'Generador' => '']">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Generador de imágenes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Compón una imagen comercial a partir de una plantilla, tus productos y textos.</p>
        </div>
        <div class="flex gap-2">
            @can('ver imagenes')
                <a href="{{ route('images.templates.index') }}" class="nodo-btn-secondary">Plantillas</a>
                <a href="{{ route('images.history') }}" class="nodo-btn-secondary">Historial</a>
            @endcan
        </div>
    </div>

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('images.generate') }}" enctype="multipart/form-data"
          x-data="{
              backgroundSource: 'color',
              productId: '{{ $selectedProduct?->id }}',
              products: @js($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'url' => $p->url, 'whatsapp_url' => $p->whatsapp_url])),
              get selectedProductData() { return this.products.find(p => p.id == this.productId) || null; }
          }"
          class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @csrf

        <div class="nodo-card space-y-5 p-6 lg:col-span-2">
            <div>
                <label class="nodo-label">Plantilla</label>
                <select name="template_id" class="nodo-input" required>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}" {{ request('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }} — {{ \App\Models\ImageTemplate::FORMATS[$template->format]['label'] ?? '' }}{{ $template->is_master ? ' (maestra)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="nodo-label">Producto relacionado (opcional)</label>
                <select name="product_id" x-model="productId" class="nodo-input">
                    <option value="">Sin producto</option>
                    <template x-for="p in products" :key="p.id">
                        <option :value="p.id" x-text="p.name" :selected="p.id == productId"></option>
                    </template>
                </select>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="nodo-label">Título</label>
                    <input name="title" value="{{ old('title', $selectedProduct?->name) }}" maxlength="120" class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">Precio (texto, opcional)</label>
                    <input name="price_text" value="{{ old('price_text') }}" maxlength="60" class="nodo-input" placeholder="Desde $4,990 MXN">
                </div>
            </div>

            <div>
                <label class="nodo-label">Subtítulo</label>
                <input name="subtitle" value="{{ old('subtitle', $selectedProduct?->short_description ?? '') }}" maxlength="200" class="nodo-input">
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="nodo-label">Texto de llamada a la acción</label>
                    <input name="cta_text" value="{{ old('cta_text', 'Agenda una demostración') }}" maxlength="60" class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">URL del código QR (opcional)</label>
                    <input name="qr_target_url" value="{{ old('qr_target_url', $selectedProduct?->whatsapp_url ?? $selectedProduct?->url) }}" class="nodo-input" placeholder="https://…">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-5 dark:border-slate-800">
                <label class="nodo-label">Origen del fondo</label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-700" :class="backgroundSource === 'color' && 'ring-2 ring-blue-600'">
                        <input type="radio" name="background_source" value="color" x-model="backgroundSource" class="text-blue-600"> Degradado
                    </label>
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-700" :class="backgroundSource === 'upload' && 'ring-2 ring-blue-600'">
                        <input type="radio" name="background_source" value="upload" x-model="backgroundSource" class="text-blue-600"> Subir imagen
                    </label>
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-700" :class="backgroundSource === 'product_image' && 'ring-2 ring-blue-600'">
                        <input type="radio" name="background_source" value="product_image" x-model="backgroundSource" class="text-blue-600"> Imagen del producto
                    </label>
                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 p-3 text-sm dark:border-slate-700" :class="backgroundSource === 'ai' && 'ring-2 ring-blue-600'">
                        <input type="radio" name="background_source" value="ai" x-model="backgroundSource" class="text-blue-600"> Generar con IA
                    </label>
                </div>

                <div x-show="backgroundSource === 'upload'" x-cloak class="mt-4">
                    <input type="file" name="background_upload" accept="image/png,image/jpeg,image/webp" class="nodo-input">
                </div>

                <div x-show="backgroundSource === 'ai'" x-cloak class="mt-4">
                    @if (! $aiImagesAvailable)
                        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                            La generación de fondos con IA requiere un proveedor OpenAI configurado y habilitado en
                            @can('configurar ia')<a href="{{ route('admin.ai.settings.edit') }}" class="underline">Configuración de IA</a>.@else Configuración de IA (pídele a un administrador que la active). @endcan
                        </p>
                    @endif
                    <label class="nodo-label mt-2">Describe el fondo que quieres generar</label>
                    <textarea name="ai_prompt" rows="3" class="nodo-input" placeholder="Ilustración minimalista de un agente virtual atendiendo WhatsApp, gradiente azul y violeta"></textarea>
                </div>

                <div x-show="backgroundSource === 'product_image'" x-cloak class="mt-4 text-xs text-slate-400">
                    Se usará la imagen principal del producto seleccionado. Si el producto no tiene imagen, se usará el degradado de la plantilla.
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="nodo-btn-primary">Generar imagen</button>
            </div>
        </div>

        <div class="nodo-card p-6">
            <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Consejos</h2>
            <ul class="space-y-3 text-sm text-slate-500 dark:text-slate-400">
                <li>Elige la <strong>Plantilla maestra NODO 360</strong> para mantener la identidad visual de marca.</li>
                <li>Si seleccionas un producto, el título y subtítulo se sugieren automáticamente — puedes editarlos.</li>
                <li>El QR es útil para publicaciones impresas o historias: apúntalo al enlace de WhatsApp o a la URL del producto.</li>
                <li>La generación de fondo con IA usa la misma clave configurada en Inteligencia Artificial.</li>
            </ul>
        </div>
    </form>
</x-layouts.app>
