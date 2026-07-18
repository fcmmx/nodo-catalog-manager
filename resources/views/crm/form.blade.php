<x-layouts.app :title="($deal->exists ? 'Editar' : 'Nuevo').' prospecto · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'CRM' => route('crm.index'), ($deal->exists ? $deal->title : 'Nuevo') => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $deal->exists ? 'Editar prospecto' : 'Nuevo prospecto' }}</h1>
            @if ($deal->exists)
                <p class="text-sm text-slate-500 dark:text-slate-400">Estado: {{ \App\Models\CrmDeal::STATUSES[$deal->status] ?? $deal->status }} — Etapa: {{ $deal->stage->name }}</p>
            @endif
        </div>
        @if ($deal->exists)
            <div class="flex gap-2">
                @if ($deal->whatsappUrl())
                    <a href="{{ $deal->whatsappUrl() }}" target="_blank" class="nodo-btn-secondary">Chat por WhatsApp</a>
                @endif
                @can('editar crm')
                    @if ($deal->status === 'abierto')
                        <form method="POST" action="{{ route('crm.mark-won', $deal) }}">
                            @csrf
                            <button type="submit" class="nodo-btn-secondary text-emerald-700">Marcar ganado</button>
                        </form>
                        <button type="button" onclick="document.getElementById('lost-form').classList.toggle('hidden')" class="nodo-btn-secondary text-red-600">Marcar perdido</button>
                    @endif
                @endcan
            </div>
        @endif
    </div>

    @if ($deal->exists && $deal->status === 'abierto')
        <form id="lost-form" method="POST" action="{{ route('crm.mark-lost', $deal) }}" class="mb-6 hidden nodo-card p-4">
            @csrf
            <label class="nodo-label">Motivo de la pérdida (opcional)</label>
            <div class="flex gap-2">
                <input type="text" name="lost_reason" class="nodo-input">
                <button type="submit" class="nodo-btn-primary shrink-0">Confirmar pérdida</button>
            </div>
        </form>
    @endif

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
        <div class="lg:col-span-2 space-y-6">
            <div class="nodo-card p-6">
                <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Datos del prospecto</h2>
                <form method="POST" action="{{ $deal->exists ? route('crm.update', $deal) : route('crm.store') }}" class="space-y-5">
                    @csrf
                    @if ($deal->exists) @method('PUT') @endif

                    <div>
                        <label class="nodo-label">Título de la oportunidad</label>
                        <input name="title" value="{{ old('title', $deal->title) }}" required class="nodo-input">
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Contacto</label>
                            <select name="contact_id" required class="nodo-input">
                                <option value="">Selecciona un contacto…</option>
                                @foreach ($contacts as $contact)
                                    <option value="{{ $contact->id }}" {{ (string) old('contact_id', $deal->contact_id) === (string) $contact->id ? 'selected' : '' }}>{{ $contact->name ?: $contact->email }}</option>
                                @endforeach
                            </select>
                            @if (! $deal->exists)
                                <p class="mt-1 text-xs text-slate-400">¿No está en la lista? <a href="{{ route('email.contacts.create') }}" class="text-blue-600 hover:underline">Crea el contacto primero</a>.</p>
                            @endif
                        </div>
                        <div>
                            <label class="nodo-label">Producto relacionado (opcional)</label>
                            <select name="product_id" class="nodo-input">
                                <option value="">Sin producto</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ (string) old('product_id', $deal->product_id) === (string) $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                        <div>
                            <label class="nodo-label">Etapa</label>
                            <select name="stage_id" required class="nodo-input">
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}" {{ (string) old('stage_id', $deal->stage_id) === (string) $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="nodo-label">Valor estimado</label>
                            <input type="number" step="0.01" name="value" value="{{ old('value', $deal->value) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Moneda</label>
                            <input name="currency" value="{{ old('currency', $deal->currency ?? 'MXN') }}" maxlength="3" class="nodo-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="nodo-label">Fecha estimada de cierre</label>
                            <input type="date" name="expected_close_date" value="{{ old('expected_close_date', $deal->expected_close_date?->format('Y-m-d')) }}" class="nodo-input">
                        </div>
                        <div>
                            <label class="nodo-label">Asignado a</label>
                            <select name="assigned_to" class="nodo-input">
                                <option value="">Sin asignar</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ (string) old('assigned_to', $deal->assigned_to) === (string) $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between pt-2">
                        <a href="{{ route('crm.index') }}" class="nodo-btn-secondary">Cancelar</a>
                        <button type="submit" class="nodo-btn-primary">Guardar</button>
                    </div>
                </form>
            </div>

            @if ($deal->exists)
                <div class="nodo-card p-6">
                    <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Actividades y recordatorios</h2>

                    <form method="POST" action="{{ route('crm.activities.store', $deal) }}" class="mb-6 space-y-3 rounded-lg border border-slate-100 p-4 dark:border-slate-800">
                        @csrf
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <select name="type" class="nodo-input">
                                @foreach (\App\Models\CrmActivity::TYPES as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="datetime-local" name="due_at" class="nodo-input" placeholder="Fecha límite (para tareas)">
                        </div>
                        <textarea name="content" rows="2" placeholder="Detalle de la actividad…" class="nodo-input"></textarea>
                        <button type="submit" class="nodo-btn-secondary">Agregar actividad</button>
                    </form>

                    @if ($deal->activities->isEmpty())
                        <p class="text-sm text-slate-400">Todavía no hay actividades registradas.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($deal->activities as $activity)
                                <div class="flex items-start justify-between gap-3 rounded-lg border border-slate-100 p-3 dark:border-slate-800">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="nodo-badge bg-slate-100 text-slate-600 dark:bg-slate-800">{{ \App\Models\CrmActivity::TYPES[$activity->type] ?? $activity->type }}</span>
                                            @if ($activity->isReminder())
                                                @if ($activity->completed_at)
                                                    <span class="nodo-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">Completada</span>
                                                @elseif ($activity->isOverdue())
                                                    <span class="nodo-badge bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-300">Vencida</span>
                                                @else
                                                    <span class="nodo-badge bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300">Pendiente: {{ $activity->due_at->format('d/m/Y H:i') }}</span>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($activity->content)
                                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ $activity->content }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-slate-400">{{ $activity->user?->name ?? 'Sistema' }} — {{ $activity->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex shrink-0 gap-2 text-xs">
                                        @if ($activity->isReminder() && ! $activity->completed_at)
                                            <form method="POST" action="{{ route('crm.activities.complete', $activity) }}">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:underline">Completar</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('crm.activities.destroy', $activity) }}" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            @if ($deal->exists)
                <div class="nodo-card p-6">
                    <h2 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Contacto</h2>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $deal->contact->name ?: '—' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $deal->contact->email }}</p>
                    @if ($deal->contact->phone)
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $deal->contact->phone }}</p>
                    @endif
                    @can('editar contactos')
                        <a href="{{ route('email.contacts.edit', $deal->contact) }}" class="mt-2 inline-block text-xs text-blue-600 hover:underline">Ver ficha completa →</a>
                    @endcan

                    @if ($deal->source === 'landing' && $deal->landingLead)
                        <div class="mt-4 border-t border-slate-100 pt-4 dark:border-slate-800">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Origen</p>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Landing page: {{ $deal->landingLead->landingPage->name }}</p>
                        </div>
                    @endif
                </div>

                <div class="nodo-card p-6">
                    <a href="{{ route('crm.index') }}" class="nodo-btn-secondary w-full text-center">← Volver al pipeline</a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
