<x-layouts.install title="Administrador · Instalación" :step="4">
    <h2 class="mb-2 text-lg font-semibold text-slate-900">Crea tu cuenta de superadministrador</h2>
    <p class="mb-6 text-sm text-slate-500">Con esta cuenta iniciarás sesión por primera vez. Guarda bien tu contraseña.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('install.admin.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="nodo-label">Nombre completo</label>
            <input name="name" value="{{ old('name') }}" required class="nodo-input">
        </div>
        <div>
            <label class="nodo-label">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="nodo-input">
        </div>
        <div>
            <label class="nodo-label">Contraseña</label>
            <input type="password" name="password" required class="nodo-input" minlength="8">
        </div>
        <div>
            <label class="nodo-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required class="nodo-input" minlength="8">
        </div>

        <div class="flex justify-between pt-2">
            <a href="{{ route('install.company.form') }}" class="nodo-btn-secondary">Atrás</a>
            <button type="submit" class="nodo-btn-primary">Instalar el sistema</button>
        </div>
    </form>
</x-layouts.install>
