@php
    $initialBlocks = old('blocks') ? json_decode(old('blocks'), true) : ($campaign->blocks ?? []);
    $editable = ! $campaign->exists || in_array($campaign->status, ['borrador', 'programada', 'pausada']);
@endphp
<x-layouts.app :title="($campaign->exists ? 'Editar' : 'Nueva').' campaña · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), ($campaign->exists ? $campaign->name : 'Nueva') => '']">
    <div x-data="campaignBuilder(@js($initialBlocks))">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $campaign->exists ? 'Editar campaña' : 'Nueva campaña' }}</h1>
                @if ($campaign->exists)
                    <p class="text-sm text-slate-500 dark:text-slate-400">Estado: {{ \App\Models\EmailCampaign::STATUSES[$campaign->status] ?? $campaign->status }}</p>
                @endif
            </div>
            @if ($campaign->exists)
                <a href="{{ route('email.campaigns.report', $campaign) }}" class="nodo-btn-secondary">Ver reporte</a>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <form method="POST" action="{{ $campaign->exists ? route('email.campaigns.update', $campaign) : route('email.campaigns.store') }}" @submit="serialize()" class="space-y-6">
                    @csrf
                    @if ($campaign->exists) @method('PUT') @endif

                    <div class="nodo-card p-6">
                        <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Datos de la campaña</h2>

                        <div class="space-y-5">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="nodo-label">Nombre interno</label>
                                    <input name="name" value="{{ old('name', $campaign->name) }}" required class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                </div>
                                <div>
                                    <label class="nodo-label">Tipo</label>
                                    <select name="type" class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                        @foreach (\App\Models\EmailCampaign::TYPES as $value => $label)
                                            <option value="{{ $value }}" {{ old('type', $campaign->type ?? 'newsletter') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="nodo-label">Asunto del correo</label>
                                <input name="subject" value="{{ old('subject', $campaign->subject) }}" required class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="nodo-label">Nombre del remitente</label>
                                    <input name="from_name" value="{{ old('from_name', $campaign->from_name ?? 'NODO 360 MARKETING TECHNOLOGY') }}" required class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                </div>
                                <div>
                                    <label class="nodo-label">Correo del remitente</label>
                                    <input type="email" name="from_email" value="{{ old('from_email', $campaign->from_email) }}" required class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label class="nodo-label">Lista de contactos</label>
                                    <select name="contact_list_id" class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                        <option value="">Selecciona una lista…</option>
                                        @foreach ($lists as $list)
                                            <option value="{{ $list->id }}" {{ (string) old('contact_list_id', $campaign->contact_list_id) === (string) $list->id ? 'selected' : '' }}>{{ $list->name }} ({{ $list->contacts_count }})</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-slate-400">Requerida para programar o enviar la campaña.</p>
                                </div>
                                <div>
                                    <label class="nodo-label">Tamaño de lote por minuto</label>
                                    <input type="number" name="batch_limit" min="5" max="500" value="{{ old('batch_limit', $campaign->batch_limit ?? 50) }}" class="nodo-input" {{ $editable ? '' : 'disabled' }}>
                                    <p class="mt-1 text-xs text-slate-400">Correos enviados cada minuto por el cron, para respetar límites del proveedor SMTP.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nodo-card p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Contenido del correo</h2>
                            <div class="flex items-center gap-2">
                                <select x-ref="blockType" class="nodo-input w-auto text-xs" {{ $editable ? '' : 'disabled' }}>
                                    @foreach (\App\Models\EmailCampaign::BLOCK_TYPES as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="button" @click="addBlock($refs.blockType.value)" class="nodo-btn-secondary text-xs" {{ $editable ? '' : 'disabled' }}>+ Agregar bloque</button>
                            </div>
                        </div>

                        <template x-if="blocks.length === 0">
                            <p class="rounded-xl border border-dashed border-slate-200 py-10 text-center text-sm text-slate-400 dark:border-slate-800">Agrega bloques para construir el contenido del correo.</p>
                        </template>

                        <div class="space-y-3">
                            <template x-for="(block, index) in blocks" :key="index">
                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-400" x-text="blockTypes[block.type] || block.type"></span>
                                        <div class="flex items-center gap-2 text-xs">
                                            <button type="button" @click="moveUp(index)" class="text-slate-400 hover:text-slate-700">↑</button>
                                            <button type="button" @click="moveDown(index)" class="text-slate-400 hover:text-slate-700">↓</button>
                                            <button type="button" @click="removeBlock(index)" class="text-red-500 hover:text-red-700">Quitar</button>
                                        </div>
                                    </div>

                                    <template x-if="block.type === 'header'">
                                        <div class="space-y-2">
                                            <input type="text" x-model="block.title" placeholder="Título" class="nodo-input">
                                            <input type="text" x-model="block.subtitle" placeholder="Subtítulo (opcional)" class="nodo-input">
                                        </div>
                                    </template>

                                    <template x-if="block.type === 'text'">
                                        <textarea x-model="block.content" rows="4" placeholder="Contenido del bloque de texto…" class="nodo-input"></textarea>
                                    </template>

                                    <template x-if="block.type === 'image'">
                                        <div class="space-y-2">
                                            <input type="text" x-model="block.url" placeholder="URL de la imagen" class="nodo-input">
                                            <input type="text" x-model="block.alt" placeholder="Texto alternativo" class="nodo-input">
                                            <input type="text" x-model="block.link" placeholder="Enlace al hacer clic (opcional)" class="nodo-input">
                                        </div>
                                    </template>

                                    <template x-if="block.type === 'button'">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <input type="text" x-model="block.text" placeholder="Texto del botón" class="nodo-input">
                                            <input type="text" x-model="block.url" placeholder="URL de destino" class="nodo-input">
                                        </div>
                                    </template>

                                    <template x-if="block.type === 'products'">
                                        <div class="max-h-48 space-y-1 overflow-y-auto rounded-lg border border-slate-100 p-2 dark:border-slate-800">
                                            @forelse ($products as $product)
                                                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                    <input type="checkbox" @change="toggleProduct(index, {{ $product->id }})" :checked="(block.product_ids || []).includes({{ $product->id }})" class="rounded border-slate-300 text-blue-600">
                                                    {{ $product->name }}
                                                </label>
                                            @empty
                                                <p class="text-xs text-slate-400">No hay productos en el catálogo todavía.</p>
                                            @endforelse
                                        </div>
                                    </template>

                                    <template x-if="block.type === 'divider'">
                                        <p class="text-xs text-slate-400">Línea divisoria — sin opciones.</p>
                                    </template>

                                    <template x-if="block.type === 'social'">
                                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                            <input type="text" x-model="block.facebook" placeholder="URL de Facebook" class="nodo-input">
                                            <input type="text" x-model="block.instagram" placeholder="URL de Instagram" class="nodo-input">
                                            <input type="text" x-model="block.linkedin" placeholder="URL de LinkedIn" class="nodo-input">
                                            <input type="text" x-model="block.x" placeholder="URL de X" class="nodo-input">
                                        </div>
                                    </template>

                                    <template x-if="block.type === 'footer'">
                                        <input type="text" x-model="block.text" placeholder="Texto del pie legal" class="nodo-input">
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="blocks" id="blocks_json">

                    <div class="flex justify-between">
                        <a href="{{ route('email.campaigns.index') }}" class="nodo-btn-secondary">Cancelar</a>
                        @if ($editable)
                            <button type="submit" class="nodo-btn-primary">Guardar</button>
                        @endif
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                @if ($campaign->exists)
                    @can('enviar campanas')
                        <div class="nodo-card p-6">
                            <h2 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Enviar prueba</h2>
                            <form method="POST" action="{{ route('email.campaigns.send-test', $campaign) }}" class="space-y-3">
                                @csrf
                                <input type="email" name="test_email" required placeholder="tu-correo@ejemplo.com" class="nodo-input">
                                <button type="submit" class="nodo-btn-secondary w-full">Enviar correo de prueba</button>
                            </form>
                        </div>

                        @if (in_array($campaign->status, ['borrador', 'programada', 'pausada']))
                            <div class="nodo-card p-6">
                                <h2 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Programar envío</h2>
                                <form method="POST" action="{{ route('email.campaigns.schedule', $campaign) }}" class="space-y-3">
                                    @csrf
                                    <input type="datetime-local" name="scheduled_at" required class="nodo-input" value="{{ $campaign->scheduled_at?->format('Y-m-d\TH:i') }}">
                                    <button type="submit" class="nodo-btn-secondary w-full">Programar</button>
                                </form>
                                <form method="POST" action="{{ route('email.campaigns.send-now', $campaign) }}" class="mt-3" onsubmit="return confirm('¿Enviar esta campaña ahora mismo a toda la lista?');">
                                    @csrf
                                    <button type="submit" class="nodo-btn-primary w-full">Enviar ahora</button>
                                </form>
                            </div>
                        @endif

                        @if (in_array($campaign->status, ['programada', 'enviando']))
                            <div class="nodo-card p-6">
                                <form method="POST" action="{{ route('email.campaigns.pause', $campaign) }}">
                                    @csrf
                                    <button type="submit" class="nodo-btn-secondary w-full">Pausar campaña</button>
                                </form>
                            </div>
                        @endif
                    @endcan
                @else
                    <div class="nodo-card p-6 text-sm text-slate-500 dark:text-slate-400">
                        Guarda la campaña primero para poder enviar pruebas, programarla o enviarla.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function campaignBuilder(initialBlocks) {
            return {
                blocks: Array.isArray(initialBlocks) ? initialBlocks : [],
                blockTypes: @json(\App\Models\EmailCampaign::BLOCK_TYPES),
                defaults() {
                    return {
                        header: { type: 'header', title: '', subtitle: '' },
                        text: { type: 'text', content: '' },
                        image: { type: 'image', url: '', alt: '', link: '' },
                        button: { type: 'button', text: 'Ver más', url: '' },
                        products: { type: 'products', product_ids: [] },
                        divider: { type: 'divider' },
                        social: { type: 'social', facebook: '', instagram: '', linkedin: '', x: '' },
                        footer: { type: 'footer', text: 'NODO 360 MARKETING TECHNOLOGY' },
                    };
                },
                addBlock(type) {
                    const d = this.defaults()[type];
                    if (! d) return;
                    this.blocks.push(JSON.parse(JSON.stringify(d)));
                },
                removeBlock(index) {
                    this.blocks.splice(index, 1);
                },
                moveUp(index) {
                    if (index === 0) return;
                    const b = this.blocks.splice(index, 1)[0];
                    this.blocks.splice(index - 1, 0, b);
                },
                moveDown(index) {
                    if (index === this.blocks.length - 1) return;
                    const b = this.blocks.splice(index, 1)[0];
                    this.blocks.splice(index + 1, 0, b);
                },
                toggleProduct(index, productId) {
                    const ids = this.blocks[index].product_ids || [];
                    const pos = ids.indexOf(productId);
                    if (pos === -1) { ids.push(productId); } else { ids.splice(pos, 1); }
                    this.blocks[index].product_ids = ids;
                },
                serialize() {
                    document.getElementById('blocks_json').value = JSON.stringify(this.blocks);
                },
            };
        }
    </script>
</x-layouts.app>
