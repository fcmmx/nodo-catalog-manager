<x-layouts.app :title="($contact->exists ? 'Editar' : 'Nuevo').' contacto · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Email Marketing' => route('email.campaigns.index'), 'Contactos' => route('email.contacts.index'), ($contact->exists ? 'Editar' : 'Nuevo') => '']">
    <div class="mx-auto max-w-2xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $contact->exists ? 'Editar contacto' : 'Nuevo contacto' }}</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $contact->exists ? route('email.contacts.update', $contact) : route('email.contacts.store') }}" class="space-y-5">
                @csrf
                @if ($contact->exists) @method('PUT') @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Nombre</label>
                        <input name="name" value="{{ old('name', $contact->name) }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Correo electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $contact->email) }}" required class="nodo-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Empresa</label>
                        <input name="company" value="{{ old('company', $contact->company) }}" class="nodo-input">
                    </div>
                    <div>
                        <label class="nodo-label">Teléfono</label>
                        <input name="phone" value="{{ old('phone', $contact->phone) }}" class="nodo-input">
                    </div>
                </div>

                <div>
                    <label class="nodo-label">WhatsApp</label>
                    <input name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}" class="nodo-input" placeholder="+52 55 1234 5678">
                </div>

                <div>
                    <label class="nodo-label">Listas</label>
                    <div class="flex flex-wrap gap-3 rounded-lg border border-slate-200 px-3 py-2 dark:border-slate-800">
                        @forelse ($lists as $list)
                            <label class="flex items-center gap-1.5 text-sm text-slate-600 dark:text-slate-300">
                                <input type="checkbox" name="list_ids[]" value="{{ $list->id }}" {{ in_array($list->id, old('list_ids', $contact->lists->pluck('id')->all())) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                                {{ $list->name }}
                            </label>
                        @empty
                            <p class="text-sm text-slate-400">No hay listas todavía. <a href="{{ route('email.lists.create') }}" class="text-blue-600 hover:underline">Crea una</a>.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="nodo-label">Notas</label>
                    <textarea name="notes" rows="3" class="nodo-input">{{ old('notes', $contact->notes) }}</textarea>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="consent" value="1" {{ old('consent', $contact->consent ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Cuenta con consentimiento para recibir correos
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="subscribed" value="1" {{ old('subscribed', $contact->subscribed ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Suscrito (activo)
                    </label>
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('email.contacts.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
