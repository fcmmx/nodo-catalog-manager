@php
    $initialSections = old('sections') ? json_decode(old('sections'), true) : ($landing->sections ?? []);
@endphp
<x-layouts.app :title="($landing->exists ? 'Editar' : 'Nueva').' landing page · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Landing Pages' => route('landing.index'), ($landing->exists ? $landing->name : 'Nueva') => '']">
    <div x-data="landingBuilder(@js($initialSections))">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $landing->exists ? 'Editar landing page' : 'Nueva landing page' }}</h1>
                @if ($landing->exists)
                    <p class="text-sm text-slate-500 dark:text-slate-400">Estado: {{ \App\Models\LandingPage::STATUSES[$landing->status] ?? $landing->status }}
                        @if ($landing->isPublished())
                            — <a href="{{ $landing->publicUrl() }}" target="_blank" class="text-blue-600 hover:underline">{{ $landing->publicUrl() }} ↗</a>
                        @endif
                    </p>
                @endif
            </div>
            @if ($landing->exists)
                <div class="flex gap-2">
                    <a href="{{ route('landing.leads', $landing) }}" class="nodo-btn-secondary">Ver prospectos</a>
                    <a href="{{ route('landing.qr', $landing) }}" class="nodo-btn-secondary">Descargar QR</a>
                    @can('publicar landing')
                        @if ($landing->isPublished())
                            <form method="POST" action="{{ route('landing.unpublish', $landing) }}">
                                @csrf
                                <button type="submit" class="nodo-btn-secondary">Despublicar</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('landing.publish', $landing) }}">
                                @csrf
                                <button type="submit" class="nodo-btn-primary">Publicar</button>
                            </form>
                        @endif
                    @endcan
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $landing->exists ? route('landing.update', $landing) : route('landing.store') }}" enctype="multipart/form-data" @submit="serialize()" class="space-y-6">
            @csrf
            @if ($landing->exists) @method('PUT') @endif

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Datos generales</h2>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Nombre interno</label>
                            <input name="name" value="{{ old('name', $landing->name) }}" required class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Producto relacionado (opcional)</label>
                            <select name="product_id" class="nodo-input">
                                <option value="">Sin producto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ (string) old('product_id', $landing->product_id) === (string) $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="nodo-label">Titular (hero)</label>
                        <input name="headline" value="{{ old('headline', $landing->headline) }}" required class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Subtitular (hero)</label>
                        <input name="subheadline" value="{{ old('subheadline', $landing->subheadline) }}" class="nodo-input">
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Imagen principal (hero)</label>
                            @if ($landing->hero_image_path)
                                <img src="{{ $landing->heroImageUrl() }}" class="mb-2 aspect-video w-full max-w-xs rounded-lg object-cover">
                            @endif
                            <input type="file" name="hero_image" accept="image/png,image/jpeg,image/webp" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Imagen para redes (Open Graph, opcional)</label>
                            @if ($landing->og_image_path)
                                <img src="{{ $landing->ogImageUrl() }}" class="mb-2 aspect-video w-full max-w-xs rounded-lg object-cover">
                            @endif
                            <input type="file" name="og_image" accept="image/png,image/jpeg,image/webp" class="nodo-input">
                            <p class="mt-1 text-xs text-slate-400">Si no subes una, se usa la imagen principal.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Llamada a la acción</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Texto del botón</label>
                        <input name="cta_text" value="{{ old('cta_text', $landing->cta_text ?? 'Quiero más información') }}" required class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Enlace externo (opcional)</label>
                        <input name="cta_url" value="{{ old('cta_url', $landing->cta_url) }}" class="nodo-input" placeholder="https://...">
                    </div>
                    <div>
                        <label class="nodo-label">WhatsApp (número)</label>
                        <input name="cta_whatsapp_number" value="{{ old('cta_whatsapp_number', $landing->cta_whatsapp_number) }}" class="nodo-input" placeholder="+52 55 1234 5678">
                    </div>
                    <div>
                        <label class="nodo-label">Mensaje predefinido de WhatsApp</label>
                        <input name="cta_whatsapp_message" value="{{ old('cta_whatsapp_message', $landing->cta_whatsapp_message) }}" class="nodo-input">
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-400">Si defines WhatsApp, el botón abre un chat directo; si no, usa el enlace externo. Puedes definir ambos y usarlos en distintas secciones.</p>
            </div>

            <div class="nodo-card p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Contenido de la página</h2>
                    <div class="flex items-center gap-2">
                        <select x-ref="sectionType" class="nodo-input w-auto text-xs">
                            @foreach (\App\Models\LandingPage::SECTION_TYPES as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="addSection($refs.sectionType.value)" class="nodo-btn-secondary text-xs">+ Agregar sección</button>
                    </div>
                </div>

                <template x-if="sections.length === 0">
                    <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center text-sm text-slate-400 dark:border-slate-800">Agrega secciones para construir el contenido de la landing page.</p>
                </template>

                <div class="space-y-3">
                    <template x-for="(section, index) in sections" :key="index">
                        <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" x-text="sectionTypes[section.type] || section.type"></span>
                                <div class="flex items-center gap-2 text-xs">
                                    <button type="button" @click="moveUp(index)" class="text-slate-400 hover:text-slate-700">↑</button>
                                    <button type="button" @click="moveDown(index)" class="text-slate-400 hover:text-slate-700">↓</button>
                                    <button type="button" @click="removeSection(index)" class="text-red-500 hover:text-red-700">Quitar</button>
                                </div>
                            </div>

                            <template x-if="['problema','solucion','beneficios','caracteristicas','testimonios','faq'].includes(section.type)">
                                <div class="space-y-3">
                                    <input type="text" x-model="section.title" placeholder="Título de la sección" class="nodo-input">
                                    <template x-for="(item, itemIndex) in section.items" :key="itemIndex">
                                        <div class="flex items-start gap-2 rounded-lg border border-slate-100 p-2 dark:border-slate-800">
                                            <div class="flex-1 space-y-1">
                                                <input type="text" x-model="item.heading" placeholder="Encabezado (p. ej. pregunta, nombre, beneficio)" class="nodo-input text-sm">
                                                <textarea x-model="item.text" rows="2" placeholder="Texto" class="nodo-input text-sm"></textarea>
                                            </div>
                                            <button type="button" @click="section.items.splice(itemIndex, 1)" class="mt-1 text-xs text-red-500 hover:text-red-700">✕</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="section.items.push({ heading: '', text: '' })" class="text-xs font-medium text-blue-600 hover:underline">+ Agregar elemento</button>
                                </div>
                            </template>

                            <template x-if="section.type === 'texto'">
                                <div class="space-y-2">
                                    <input type="text" x-model="section.title" placeholder="Título (opcional)" class="nodo-input">
                                    <textarea x-model="section.content" rows="4" placeholder="Contenido…" class="nodo-input"></textarea>
                                </div>
                            </template>

                            <template x-if="section.type === 'imagen'">
                                <div class="space-y-2">
                                    <input type="text" x-model="section.url" placeholder="URL de la imagen" class="nodo-input">
                                    <input type="text" x-model="section.alt" placeholder="Texto alternativo" class="nodo-input">
                                </div>
                            </template>

                            <template x-if="section.type === 'video'">
                                <input type="text" x-model="section.video_url" placeholder="URL de YouTube o Vimeo" class="nodo-input">
                            </template>

                            <template x-if="section.type === 'producto'">
                                <p class="text-xs text-slate-400">Se mostrará automáticamente la información del producto relacionado (imagen, nombre, precio, descripción).</p>
                            </template>

                            <template x-if="section.type === 'cta'">
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <input type="text" x-model="section.text" placeholder="Texto del botón" class="nodo-input">
                                    <input type="text" x-model="section.url" placeholder="URL de destino (opcional, si no usa el CTA principal)" class="nodo-input">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">SEO y datos estructurados</h2>
                <div class="space-y-5">
                    <div>
                        <label class="nodo-label">Título SEO (meta title)</label>
                        <input name="meta_title" value="{{ old('meta_title', $landing->meta_title) }}" class="nodo-input" maxlength="255">
                    </div>
                    <div>
                        <label class="nodo-label">Descripción SEO (meta description)</label>
                        <textarea name="meta_description" rows="2" class="nodo-input" maxlength="500">{{ old('meta_description', $landing->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Analítica (opcional)</h2>
                <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Solo se inyecta en la página pública el código de lo que configures aquí. Si lo dejas vacío, no se carga ningún script de seguimiento.</p>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label class="nodo-label">Google Analytics 4 (ID de medición)</label>
                        <input name="ga4_id" value="{{ old('ga4_id', $landing->ga4_id) }}" class="nodo-input" placeholder="G-XXXXXXXXXX">
                    </div>
                    <div>
                        <label class="nodo-label">Meta Pixel (ID)</label>
                        <input name="meta_pixel_id" value="{{ old('meta_pixel_id', $landing->meta_pixel_id) }}" class="nodo-input" placeholder="123456789012345">
                    </div>
                    <div>
                        <label class="nodo-label">Google Tag Manager (ID)</label>
                        <input name="gtm_id" value="{{ old('gtm_id', $landing->gtm_id) }}" class="nodo-input" placeholder="GTM-XXXXXXX">
                    </div>
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Captura de prospectos</h2>
                <label class="mb-4 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="capture_form_enabled" value="1" {{ old('capture_form_enabled', $landing->capture_form_enabled ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Mostrar formulario de contacto en la landing page
                </label>
                <div>
                    <label class="nodo-label">Agregar prospectos a la lista de contactos</label>
                    <select name="contact_list_id" class="nodo-input">
                        <option value="">No agregar a ninguna lista</option>
                        @foreach ($lists as $list)
                            <option value="{{ $list->id }}" {{ (string) old('contact_list_id', $landing->contact_list_id) === (string) $list->id ? 'selected' : '' }}>{{ $list->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Si eliges una lista, cada prospecto se crea también como contacto de email marketing (Fase 5), con consentimiento registrado.</p>
                </div>
            </div>

            <input type="hidden" name="sections" id="sections_json">

            <div class="flex justify-between">
                <a href="{{ route('landing.index') }}" class="nodo-btn-secondary">Cancelar</a>
                <button type="submit" class="nodo-btn-primary">Guardar</button>
            </div>
        </form>
    </div>

    <script>
        function landingBuilder(initialSections) {
            return {
                sections: Array.isArray(initialSections) ? initialSections : [],
                sectionTypes: @json(\App\Models\LandingPage::SECTION_TYPES),
                defaults() {
                    const listBased = { title: '', items: [] };
                    return {
                        problema: { type: 'problema', ...structuredCloneCompat(listBased) },
                        solucion: { type: 'solucion', ...structuredCloneCompat(listBased) },
                        beneficios: { type: 'beneficios', ...structuredCloneCompat(listBased) },
                        caracteristicas: { type: 'caracteristicas', ...structuredCloneCompat(listBased) },
                        testimonios: { type: 'testimonios', ...structuredCloneCompat(listBased) },
                        faq: { type: 'faq', ...structuredCloneCompat(listBased) },
                        producto: { type: 'producto' },
                        texto: { type: 'texto', title: '', content: '' },
                        imagen: { type: 'imagen', url: '', alt: '' },
                        video: { type: 'video', video_url: '' },
                        cta: { type: 'cta', text: 'Quiero más información', url: '' },
                    };
                },
                addSection(type) {
                    const d = this.defaults()[type];
                    if (! d) return;
                    this.sections.push(JSON.parse(JSON.stringify(d)));
                },
                removeSection(index) {
                    this.sections.splice(index, 1);
                },
                moveUp(index) {
                    if (index === 0) return;
                    const s = this.sections.splice(index, 1)[0];
                    this.sections.splice(index - 1, 0, s);
                },
                moveDown(index) {
                    if (index === this.sections.length - 1) return;
                    const s = this.sections.splice(index, 1)[0];
                    this.sections.splice(index + 1, 0, s);
                },
                serialize() {
                    document.getElementById('sections_json').value = JSON.stringify(this.sections);
                },
            };
        }
        function structuredCloneCompat(obj) {
            return JSON.parse(JSON.stringify(obj));
        }
    </script>
</x-layouts.app>
