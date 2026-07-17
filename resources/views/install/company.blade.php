<x-layouts.install title="Datos de la empresa · Instalación" :step="3">
    <h2 class="mb-2 text-lg font-semibold text-slate-900">Datos de la empresa</h2>
    <p class="mb-6 text-sm text-slate-500">Esta información se usará en el sistema y podrás editarla después desde Configuración.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.company.store') }}" class="space-y-5">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="nodo-label">Nombre de la empresa</label>
                <input name="company_name" value="{{ old('company_name', $old['company_name']) }}" required class="nodo-input">
            </div>
            <div>
                <label class="nodo-label">Nombre del sistema</label>
                <input name="system_name" value="{{ old('system_name', $old['system_name']) }}" required class="nodo-input">
            </div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="nodo-label">Correo de contacto</label>
                <input type="email" name="company_email" value="{{ old('company_email', $old['company_email']) }}" class="nodo-input">
            </div>
            <div>
                <label class="nodo-label">Sitio web</label>
                <input name="company_website" value="{{ old('company_website', $old['company_website']) }}" class="nodo-input">
            </div>
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="nodo-label">Teléfono</label>
                <input name="company_phone" value="{{ old('company_phone') }}" class="nodo-input">
            </div>
            <div>
                <label class="nodo-label">WhatsApp</label>
                <input name="company_whatsapp" value="{{ old('company_whatsapp') }}" class="nodo-input">
            </div>
        </div>
        <div>
            <label class="nodo-label">Dirección</label>
            <input name="company_address" value="{{ old('company_address') }}" class="nodo-input">
        </div>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="nodo-label">Moneda</label>
                <select name="currency" class="nodo-input">
                    <option value="MXN" {{ old('currency', $old['currency']) === 'MXN' ? 'selected' : '' }}>MXN — Peso mexicano</option>
                    <option value="USD" {{ old('currency', $old['currency']) === 'USD' ? 'selected' : '' }}>USD — Dólar estadounidense</option>
                </select>
            </div>
            <div>
                <label class="nodo-label">Zona horaria</label>
                <select name="timezone" class="nodo-input">
                    <option value="America/Mexico_City" {{ old('timezone', $old['timezone']) === 'America/Mexico_City' ? 'selected' : '' }}>America/Mexico_City</option>
                    <option value="America/Tijuana">America/Tijuana</option>
                    <option value="America/Cancun">America/Cancun</option>
                </select>
            </div>
        </div>

        <div class="flex justify-between pt-2">
            <a href="{{ route('install.database.form') }}" class="nodo-btn-secondary">Atrás</a>
            <button type="submit" class="nodo-btn-primary">Continuar</button>
        </div>
    </form>
</x-layouts.install>
