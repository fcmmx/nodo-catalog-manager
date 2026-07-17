<x-layouts.app title="Configuración · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Configuración' => '']">
    <div class="mx-auto max-w-3xl space-y-6">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Datos de la empresa</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Nombre de la empresa</label>
                        <input name="company_name" value="{{ old('company_name', $general['company_name'] ?? '') }}" required class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Nombre visible del sistema</label>
                        <input name="system_name" value="{{ old('system_name', $general['system_name'] ?? '') }}" required class="nodo-input">
                    </div>
                </div>
                <div class="mt-5">
                    <label class="nodo-label">Subtítulo del sistema</label>
                    <input name="system_subtitle" value="{{ old('system_subtitle', $general['system_subtitle'] ?? '') }}" class="nodo-input">
                </div>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Correo de contacto</label>
                        <input type="email" name="company_email" value="{{ old('company_email', $general['company_email'] ?? '') }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Sitio web</label>
                        <input name="company_website" value="{{ old('company_website', $general['company_website'] ?? '') }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Teléfono</label>
                        <input name="company_phone" value="{{ old('company_phone', $general['company_phone'] ?? '') }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">WhatsApp</label>
                        <input name="company_whatsapp" value="{{ old('company_whatsapp', $general['company_whatsapp'] ?? '') }}" class="nodo-input">
                    </div>
                </div>
                <div class="mt-5">
                    <label class="nodo-label">Dirección</label>
                    <input name="company_address" value="{{ old('company_address', $general['company_address'] ?? '') }}" class="nodo-input">
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Regional y moneda</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label class="nodo-label">Moneda</label>
                        <select name="currency" class="nodo-input">
                            <option value="MXN" {{ old('currency', $general['currency'] ?? 'MXN') === 'MXN' ? 'selected' : '' }}>MXN</option>
                            <option value="USD" {{ old('currency', $general['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                    <div>
                        <label class="nodo-label">Zona horaria</label>
                        <select name="timezone" class="nodo-input">
                            <option value="America/Mexico_City" {{ old('timezone', $general['timezone'] ?? '') === 'America/Mexico_City' ? 'selected' : '' }}>America/Mexico_City</option>
                            <option value="America/Tijuana" {{ old('timezone', $general['timezone'] ?? '') === 'America/Tijuana' ? 'selected' : '' }}>America/Tijuana</option>
                            <option value="America/Cancun" {{ old('timezone', $general['timezone'] ?? '') === 'America/Cancun' ? 'selected' : '' }}>America/Cancun</option>
                        </select>
                    </div>
                    <div>
                        <label class="nodo-label">IVA (%)</label>
                        <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $general['tax_rate'] ?? 16) }}" class="nodo-input">
                    </div>
                </div>
                <p class="mt-3 text-xs text-slate-400">Formato de precios: $12,345.67 {{ $general['currency'] ?? 'MXN' }}</p>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Marca e identidad visual</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Logotipo</label>
                        @if (!empty($general['logo_path']))
                            <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($general['logo_path']) }}" class="mb-2 h-10">
                        @endif
                        <input type="file" name="logo" accept="image/*" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Favicon</label>
                        @if (!empty($general['favicon_path']))
                            <img src="{{ Illuminate\Support\Facades\Storage::disk('public')->url($general['favicon_path']) }}" class="mb-2 h-8 w-8">
                        @endif
                        <input type="file" name="favicon" accept="image/png,image/x-icon" class="nodo-input">
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Color primario</label>
                        <input type="color" name="primary_color" value="{{ old('primary_color', $general['primary_color'] ?? '#0F172A') }}" class="nodo-input h-10 p-1">
                    </div>
                    <div>
                        <label class="nodo-label">Color de acento</label>
                        <input type="color" name="accent_color" value="{{ old('accent_color', $general['accent_color'] ?? '#DC2626') }}" class="nodo-input h-10 p-1">
                    </div>
                </div>
                <div class="mt-5">
                    <label class="nodo-label">Texto principal (hero)</label>
                    <textarea name="hero_text" rows="2" class="nodo-input">{{ old('hero_text', $general['hero_text'] ?? '') }}</textarea>
                </div>
                <div class="mt-5">
                    <label class="nodo-label">Texto de llamada a la acción</label>
                    <input name="cta_text" value="{{ old('cta_text', $general['cta_text'] ?? '') }}" class="nodo-input">
                </div>
            </div>

            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Seguridad de inicio de sesión</h2>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Intentos fallidos antes de bloquear</label>
                        <input type="number" name="login_max_attempts" value="{{ old('login_max_attempts', $security['login_max_attempts'] ?? 5) }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Minutos de bloqueo</label>
                        <input type="number" name="login_lockout_minutes" value="{{ old('login_lockout_minutes', $security['login_lockout_minutes'] ?? 15) }}" class="nodo-input">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="nodo-btn-primary">Guardar configuración</button>
            </div>
        </form>
    </div>
</x-layouts.app>
