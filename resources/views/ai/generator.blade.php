<x-layouts.app title="Generador de contenido IA · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Generador de contenido' => '']">
    @if (! $isConfigured)
        <div class="mb-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950 dark:text-amber-300">
            <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            <div>
                <p class="font-medium">El generador de contenido con IA no está configurado.</p>
                <p class="mt-1 text-amber-700 dark:text-amber-400">
                    @can('configurar ia')
                        <a href="{{ route('admin.ai.settings.edit') }}" class="underline">Configura un proveedor de IA</a> antes de usar esta función.
                    @else
                        Pide a un administrador que configure un proveedor de IA.
                    @endcan
                </p>
            </div>
        </div>
    @endif

    <div x-data="aiGenerator()" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Generador de contenido</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Genera textos de marketing con IA. Siempre revisa y edita el resultado antes de usarlo.</p>

            <div class="space-y-5">
                <div>
                    <label class="nodo-label">Tipo de contenido</label>
                    <select x-model="task" class="nodo-input">
                        @foreach ($tasks as $key => $task)
                            <option value="{{ $key }}">{{ $task['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <template x-if="needsInput('tema')">
                    <div>
                        <label class="nodo-label">Tema / producto</label>
                        <textarea x-model="inputs.tema" rows="3" class="nodo-input" placeholder="Ej. Agente IA para WhatsApp de NODO 360, atiende clientes 24/7…"></textarea>
                    </div>
                </template>

                <template x-if="needsInput('texto')">
                    <div>
                        <label class="nodo-label">Texto de entrada</label>
                        <textarea x-model="inputs.texto" rows="5" class="nodo-input" placeholder="Pega aquí el texto que quieres mejorar, resumir, traducir o transformar."></textarea>
                    </div>
                </template>

                <template x-if="needsInput('tono')">
                    <div>
                        <label class="nodo-label">Tono deseado</label>
                        <input x-model="inputs.tono" class="nodo-input" placeholder="Ej. más cercano, más formal, más entusiasta">
                    </div>
                </template>

                <template x-if="needsInput('idioma')">
                    <div>
                        <label class="nodo-label">Idioma destino</label>
                        <input x-model="inputs.idioma" class="nodo-input" placeholder="Ej. inglés, francés">
                    </div>
                </template>

                <template x-if="needsInput('canal')">
                    <div>
                        <label class="nodo-label">Canal</label>
                        <select x-model="inputs.canal" class="nodo-input">
                            <option>Facebook</option>
                            <option>Instagram</option>
                            <option>LinkedIn</option>
                            <option>TikTok</option>
                            <option>X</option>
                        </select>
                    </div>
                </template>

                <button @click="generate()" class="nodo-btn-primary w-full" :disabled="loading || !{{ $isConfigured ? 'true' : 'false' }}">
                    <span x-show="!loading">Generar con IA</span>
                    <span x-show="loading" x-cloak>Generando…</span>
                </button>
            </div>
        </div>

        <div class="nodo-card p-6">
            <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Resultado</h2>

            <div x-show="error" x-cloak class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300" x-text="error"></div>

            <template x-if="!result && !error">
                <p class="py-12 text-center text-sm text-slate-400">El contenido generado aparecerá aquí para que lo revises antes de usarlo.</p>
            </template>

            <template x-if="result">
                <div>
                    <textarea x-model="result" rows="12" class="nodo-input font-mono text-sm"></textarea>
                    <p class="mt-2 text-xs text-slate-400" x-show="tokens.input">
                        Tokens: <span x-text="tokens.input"></span> entrada / <span x-text="tokens.output"></span> salida
                        <span x-show="cost"> — costo aproximado $<span x-text="cost"></span> USD</span>
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button @click="copy()" class="nodo-btn-secondary text-xs">Copiar</button>
                        <button @click="generate()" class="nodo-btn-secondary text-xs">Regenerar</button>
                        <form :action="'/ia/generaciones/' + generationId + '/aprobar'" method="POST" @submit.prevent="approve()">
                            <button type="submit" class="nodo-btn-secondary text-xs text-emerald-700">Aprobar</button>
                        </form>
                        <form :action="'/ia/generaciones/' + generationId + '/rechazar'" method="POST" @submit.prevent="reject()">
                            <button type="submit" class="nodo-btn-secondary text-xs text-red-600">Rechazar</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function aiGenerator() {
            return {
                task: '{{ array_key_first($tasks) }}',
                tasksInputs: @json(collect($tasks)->map(fn ($t) => $t['inputs'])),
                inputs: { tema: '', texto: '', tono: '', idioma: '', canal: 'Facebook' },
                loading: false,
                result: null,
                error: null,
                generationId: null,
                tokens: { input: null, output: null },
                cost: null,
                needsInput(field) {
                    return (this.tasksInputs[this.task] || []).includes(field);
                },
                generate() {
                    this.loading = true;
                    this.error = null;
                    fetch('{{ route('ai.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ task: this.task, ...this.inputs }),
                    })
                        .then(r => r.json().then(data => ({ status: r.status, data })))
                        .then(({ status, data }) => {
                            this.loading = false;
                            if (status !== 200 || !data.ok) {
                                this.error = data.message || 'Ocurrió un error al generar el contenido.';
                                this.result = null;
                                return;
                            }
                            this.result = data.content;
                            this.generationId = data.id;
                            this.tokens = data.tokens || {};
                            this.cost = data.estimated_cost;
                        })
                        .catch(() => { this.loading = false; this.error = 'No se pudo contactar al servidor.'; });
                },
                copy() {
                    navigator.clipboard.writeText(this.result || '');
                },
                approve() {
                    fetch('/ia/generaciones/' + this.generationId + '/aprobar', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    });
                },
                reject() {
                    fetch('/ia/generaciones/' + this.generationId + '/rechazar', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    });
                    this.result = null;
                },
            };
        }
    </script>
</x-layouts.app>
