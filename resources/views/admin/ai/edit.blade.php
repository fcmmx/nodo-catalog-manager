<x-layouts.app title="Configuración de IA · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Configuración de IA' => '']">
    <div class="mx-auto max-w-2xl" x-data="{ testing: false, testResult: null }">
        @if (! $isConfigured)
            <div class="mb-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <div>
                    <p class="font-medium">No hay un proveedor de IA configurado todavía.</p>
                    <p class="mt-1 text-amber-700 dark:text-amber-400">El generador de contenido no funcionará hasta que guardes una clave de API real de un proveedor compatible. NODO 360 debe proporcionar esta clave — no se usa ninguna clave de prueba.</p>
                </div>
            </div>
        @endif

        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Configuración de Inteligencia Artificial</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Conecta un proveedor de IA para generar textos, SEO y prompts de imagen desde el catálogo.</p>

            <form method="POST" action="{{ route('admin.ai.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="ai_enabled" value="1" {{ $enabled ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Habilitar el generador de contenido con IA
                </label>

                <div>
                    <label class="nodo-label">Proveedor</label>
                    <select name="ai_provider" id="ai_provider" class="nodo-input" onchange="document.getElementById('base_url_hint').textContent = this.value === 'google' ? 'Ejemplo: https://generativelanguage.googleapis.com/v1beta' : 'Ejemplo: https://api.openai.com/v1 (u otro proveedor compatible con la API de OpenAI)'">
                        <option value="openai" {{ $provider === 'openai' ? 'selected' : '' }}>OpenAI (o compatible)</option>
                        <option value="google" {{ $provider === 'google' ? 'selected' : '' }}>Google (Gemini)</option>
                    </select>
                </div>

                <div>
                    <label class="nodo-label">Modelo</label>
                    <input name="ai_model" value="{{ old('ai_model', $model) }}" required class="nodo-input" placeholder="gpt-4o-mini">
                </div>

                <div>
                    <label class="nodo-label">URL base de la API</label>
                    <input name="ai_base_url" value="{{ old('ai_base_url', $baseUrl) }}" required class="nodo-input">
                    <p id="base_url_hint" class="mt-1 text-xs text-slate-400">Ejemplo: https://api.openai.com/v1 (u otro proveedor compatible con la API de OpenAI)</p>
                </div>

                <div>
                    <label class="nodo-label">Clave de API</label>
                    @if ($maskedKey)
                        <p class="mb-1.5 text-xs text-slate-500">Clave actual: <span class="font-mono">{{ $maskedKey }}</span> — deja el campo vacío para conservarla.</p>
                    @endif
                    <input type="password" name="ai_api_key" class="nodo-input" placeholder="{{ $maskedKey ? 'Dejar en blanco para no cambiarla' : 'sk-...' }}" autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-400">Se guarda cifrada en la base de datos y nunca se muestra completa.</p>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button"
                        @click="
                            testing = true; testResult = null;
                            fetch('{{ route('admin.ai.settings.test') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } })
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
