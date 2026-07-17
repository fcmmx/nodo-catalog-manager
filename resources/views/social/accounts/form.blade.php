<x-layouts.app :title="($account->exists ? 'Editar' : 'Conectar').' cuenta · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Redes Sociales' => route('social.posts.index'), 'Cuentas' => route('social.accounts.index'), ($account->exists ? 'Editar' : 'Conectar') => '']">
    <div class="mx-auto max-w-xl">
        <div class="nodo-card p-6">
            <h1 class="mb-1 text-lg font-semibold text-slate-900 dark:text-white">{{ $account->exists ? 'Editar cuenta' : 'Conectar cuenta' }}</h1>
            <p class="mb-6 text-sm text-slate-500 dark:text-slate-400">El token de acceso se guarda cifrado y nunca se muestra completo.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $account->exists ? route('social.accounts.update', $account) : route('social.accounts.store') }}" class="space-y-5">
                @csrf
                @if ($account->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Canal</label>
                    <select name="channel" class="nodo-input" {{ $account->exists ? 'disabled' : '' }}>
                        @foreach (\App\Models\SocialAccount::CHANNELS as $channel)
                            <option value="{{ $channel }}" {{ old('channel', $account->channel) === $channel ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
                        @endforeach
                    </select>
                    @if ($account->exists)<input type="hidden" name="channel" value="{{ $account->channel }}">@endif
                </div>

                <div>
                    <label class="nodo-label">Nombre de la cuenta</label>
                    <input name="label" value="{{ old('label', $account->label) }}" required class="nodo-input" placeholder="NODO 360 - Página oficial">
                </div>

                <div>
                    <label class="nodo-label">ID de cuenta / página (según la plataforma)</label>
                    <input name="external_account_id" value="{{ old('external_account_id', $account->external_account_id) }}" class="nodo-input" placeholder="Ej. ID de la página de Facebook">
                </div>

                <div>
                    <label class="nodo-label">Token de acceso</label>
                    @if ($account->exists && $account->access_token)
                        <p class="mb-1.5 text-xs text-slate-500">Ya existe un token guardado. Déjalo en blanco para conservarlo.</p>
                    @endif
                    <input type="password" name="access_token" class="nodo-input" autocomplete="new-password">
                </div>

                <div>
                    <label class="nodo-label">El token expira el (opcional)</label>
                    <input type="date" name="token_expires_at" value="{{ old('token_expires_at', $account->token_expires_at?->format('Y-m-d')) }}" class="nodo-input">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                    Cuenta activa
                </label>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('social.accounts.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
