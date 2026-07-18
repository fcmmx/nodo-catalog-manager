@php
    $csvUrl = route('feed.csv', $feedToken);
    $xmlUrl = route('feed.xml', $feedToken);
@endphp
<x-layouts.app title="Meta Commerce y feeds · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Meta Commerce' => '']">
    <div class="mx-auto max-w-2xl" x-data="{ testing: false, testResult: null }">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Meta Commerce y feeds</h1>
            <a href="{{ route('admin.commerce.history') }}" class="nodo-btn-secondary">Ver historial</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
        @endif

        <div class="nodo-card mb-6 p-6">
            <h2 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Feed de catálogo</h2>
            <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Registra una de estas URL en Meta Commerce Manager (Catálogo → Fuentes de datos → Programado) para que sincronice tu catálogo automáticamente. Solo se incluyen productos <strong>publicados</strong> con precio y enlace configurados — actualmente {{ $eligibleCount }} de {{ $totalActive }} producto(s) activos cumplen los requisitos.</p>

            <div class="space-y-3">
                <div>
                    <label class="nodo-label">Feed CSV</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ $csvUrl }}" class="nodo-input font-mono text-xs" onclick="this.select()">
                        <a href="{{ $csvUrl }}" target="_blank" class="nodo-btn-secondary shrink-0">Ver</a>
                    </div>
                </div>
                <div>
                    <label class="nodo-label">Feed XML (Google Shopping / RSS)</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ $xmlUrl }}" class="nodo-input font-mono text-xs" onclick="this.select()">
                        <a href="{{ $xmlUrl }}" target="_blank" class="nodo-btn-secondary shrink-0">Ver</a>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.commerce.settings.regenerate-token') }}" class="mt-4" onsubmit="return confirm('Esto invalidará las URLs actuales del feed. Tendrás que actualizar la fuente de datos en Meta Commerce Manager. ¿Continuar?');">
                @csrf
                <button type="submit" class="text-xs font-medium text-red-600 hover:underline">Regenerar enlace del feed (invalida el actual)</button>
            </form>
        </div>

        @if (! $isConfigured)
            <div class="mb-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <div>
                    <p class="font-medium">No hay un catálogo de Meta conectado todavía.</p>
                    <p class="mt-1 text-amber-700 dark:text-amber-400">El feed sigue funcionando sin esto (cualquier plataforma puede leerlo desde la URL de arriba). Conectar el catálogo de Meta solo habilita la prueba de conexión real contra la Graph API.</p>
                </div>
            </div>
        @endif

        <div class="nodo-card p-6">
            <h2 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Conexión con Meta Commerce Manager</h2>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Datos de tu catálogo en Meta Business Suite (Commerce Manager → Configuración → ID del catálogo). El token de acceso necesita el permiso <code>catalog_management</code>.</p>

            <form method="POST" action="{{ route('admin.commerce.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="nodo-label">ID del catálogo</label>
                    <input name="meta_catalog_id" value="{{ old('meta_catalog_id', $catalogId) }}" class="nodo-input" placeholder="123456789012345">
                </div>
                <div>
                    <label class="nodo-label">ID de la cuenta de negocio (opcional)</label>
                    <input name="meta_business_id" value="{{ old('meta_business_id', $businessId) }}" class="nodo-input">
                </div>
                <div>
                    <label class="nodo-label">Token de acceso</label>
                    @if ($hasToken)
                        <p class="mb-1.5 text-xs text-slate-500">Ya hay un token guardado — deja el campo vacío para conservarlo.</p>
                    @endif
                    <input type="password" name="meta_access_token" class="nodo-input" placeholder="{{ $hasToken ? 'Dejar en blanco para no cambiarlo' : 'EAAG...' }}" autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-400">Se guarda cifrado en la base de datos y nunca se muestra completo.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button"
                        @click="
                            testing = true; testResult = null;
                            fetch('{{ route('admin.commerce.settings.test') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
                                .then(r => r.json().then(data => ({ status: r.status, data })))
                                .then(({ data }) => { testResult = data; testing = false; })
                                .catch(() => { testResult = { ok: false, message: 'No se pudo contactar al servidor.' }; testing = false; })
                        "
                        class="nodo-btn-secondary" :disabled="testing">
                        <span x-show="!testing">Probar conexión</span>
                        <span x-show="testing" x-cloak>Probando…</span>
                    </button>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>

                <div x-show="testResult" x-cloak class="rounded-lg px-4 py-3 text-sm"
                     :class="testResult && testResult.ok ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300'">
                    <span x-text="testResult ? testResult.message : ''"></span>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
