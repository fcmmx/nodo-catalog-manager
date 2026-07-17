<x-layouts.app title="Configuración de email marketing · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Configuración' => '']">
    <div class="mx-auto max-w-2xl" x-data="{ testing: false, testResult: null }">
        @if (! $isConfigured)
            <div class="mb-6 flex items-start gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <div>
                    <p class="font-medium">No hay un proveedor de correo configurado todavía.</p>
                    <p class="mt-1 text-amber-700 dark:text-amber-400">Las campañas no se enviarán hasta que guardes credenciales SMTP reales (propias o de un proveedor como Brevo, Mailgun, SendGrid o Amazon SES) y habilites el envío. NODO 360 debe proporcionar estas credenciales — no se usa ninguna cuenta de prueba.</p>
                </div>
            </div>
        @endif

        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">Configuración de email marketing</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">Conecta un servidor SMTP dedicado para el envío de campañas, independiente del correo transaccional del sistema.</p>

            <form method="POST" action="{{ route('admin.email.settings.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="email_marketing_enabled" value="1" {{ $enabled ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Habilitar el envío de campañas de email marketing
                </label>

                <div>
                    <label class="nodo-label">Proveedor</label>
                    <select name="email_marketing_provider" class="nodo-input">
                        @foreach (['smtp' => 'SMTP propio', 'brevo' => 'Brevo (Sendinblue)', 'mailgun' => 'Mailgun', 'sendgrid' => 'SendGrid', 'ses' => 'Amazon SES', 'other' => 'Otro compatible con SMTP'] as $value => $label)
                            <option value="{{ $value }}" {{ old('email_marketing_provider', $provider) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Servidor SMTP (host)</label>
                        <input name="email_marketing_host" value="{{ old('email_marketing_host', $host) }}" required class="nodo-input" placeholder="smtp.brevo.com">
                    </div>
                    <div>
                        <label class="nodo-label">Puerto</label>
                        <input type="number" name="email_marketing_port" value="{{ old('email_marketing_port', $port) }}" required class="nodo-input" placeholder="587">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Usuario</label>
                        <input name="email_marketing_username" value="{{ old('email_marketing_username', $username) }}" required class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Cifrado</label>
                        <select name="email_marketing_encryption" class="nodo-input">
                            <option value="tls" {{ old('email_marketing_encryption', $encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('email_marketing_encryption', $encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ old('email_marketing_encryption', $encryption) === '' ? 'selected' : '' }}>Ninguno</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="nodo-label">Contraseña / clave de API</label>
                    @if ($hasPassword)
                        <p class="mb-1.5 text-xs text-slate-500">Ya hay una contraseña guardada — deja el campo vacío para conservarla.</p>
                    @endif
                    <input type="password" name="email_marketing_password" class="nodo-input" placeholder="{{ $hasPassword ? 'Dejar en blanco para no cambiarla' : '••••••••' }}" autocomplete="new-password">
                    <p class="mt-1 text-xs text-slate-400">Se guarda cifrada en la base de datos y nunca se muestra completa.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Nombre del remitente</label>
                        <input name="email_marketing_from_name" value="{{ old('email_marketing_from_name', $fromName) }}" required class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Correo del remitente</label>
                        <input type="email" name="email_marketing_from_email" value="{{ old('email_marketing_from_email', $fromEmail) }}" required class="nodo-input">
                    </div>
                </div>

                <button type="submit" class="nodo-btn-primary w-full">Guardar configuración</button>
            </form>
        </div>

        <div class="nodo-card mt-6 p-6">
            <h2 class="mb-1 text-sm font-semibold text-slate-900 dark:text-white">Probar conexión</h2>
            <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Envía un correo de prueba con la configuración guardada actualmente (guarda los cambios primero).</p>

            <div class="flex flex-wrap items-center gap-3">
                <input type="email" placeholder="tu-correo@ejemplo.com" class="nodo-input w-auto flex-1" id="test_email">
                <button type="button"
                    @click="
                        testing = true; testResult = null;
                        const email = document.getElementById('test_email').value;
                        fetch('{{ route('admin.email.settings.test') }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ test_email: email })
                        })
                            .then(r => r.json().then(data => ({ status: r.status, data })))
                            .then(({ data }) => { testResult = data; testing = false; })
                            .catch(() => { testResult = { ok: false, message: 'No se pudo contactar al servidor.' }; testing = false; })
                    "
                    class="nodo-btn-secondary" :disabled="testing">
                    <span x-show="!testing">Enviar prueba</span>
                    <span x-show="testing" x-cloak>Enviando…</span>
                </button>
            </div>

            <div x-show="testResult" x-cloak class="mt-4 rounded-lg px-4 py-3 text-sm"
                 :class="testResult && testResult.ok ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300'">
                <span x-text="testResult ? testResult.message : ''"></span>
            </div>
        </div>
    </div>
</x-layouts.app>
