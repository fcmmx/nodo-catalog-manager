<x-layouts.app title="Calendario editorial · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'Redes Sociales' => '']">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Calendario editorial</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $month->translatedFormat('F \d\e Y') }} — {{ $posts->count() }} publicación(es)</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('social.accounts.index') }}" class="nodo-btn-secondary">Cuentas</a>
            <a href="{{ route('social.posts.export') }}" class="nodo-btn-secondary">Exportar CSV</a>
            @can('crear redes')
                <a href="{{ route('social.posts.create') }}" class="nodo-btn-primary">+ Nueva publicación</a>
            @endcan
        </div>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <a href="{{ route('social.posts.index', ['mes' => $month->copy()->subMonth()->format('Y-m-01')]) }}" class="nodo-btn-secondary">← Mes anterior</a>
        <a href="{{ route('social.posts.index') }}" class="nodo-btn-secondary">Hoy</a>
        <a href="{{ route('social.posts.index', ['mes' => $month->copy()->addMonth()->format('Y-m-01')]) }}" class="nodo-btn-secondary">Mes siguiente →</a>
        <input type="hidden" name="mes" value="{{ $month->format('Y-m-d') }}">
        <select name="channel" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos los canales</option>
            @foreach ($channels as $channel)
                <option value="{{ $channel }}" {{ request('channel') === $channel ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $channel)) }}</option>
            @endforeach
        </select>
        <select name="status" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="view" class="nodo-input w-auto" onchange="this.form.submit()">
            <option value="mes" {{ request('view', 'mes') === 'mes' ? 'selected' : '' }}>Vista mensual</option>
            <option value="todas" {{ request('view') === 'todas' ? 'selected' : '' }}>Todas (lista)</option>
        </select>
    </form>

    @if ($posts->isEmpty())
        <x-ui.empty-state title="No hay publicaciones en este periodo" description="Crea una publicación o navega a otro mes." />
    @else
        @php
            $byDate = $posts->groupBy(fn ($p) => $p->scheduled_at ? $p->scheduled_at->format('Y-m-d') : 'sin_fecha');
        @endphp

        @if ($byDate->has('sin_fecha'))
            <div class="mb-6">
                <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Sin fecha programada (borradores)</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($byDate['sin_fecha'] as $post)
                        <x-social.post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        @endif

        @foreach ($byDate as $date => $dayPosts)
            @continue($date === 'sin_fecha')
            <div class="mb-6">
                <h2 class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l d \d\e F') }}</h2>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($dayPosts as $post)
                        <x-social.post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</x-layouts.app>
