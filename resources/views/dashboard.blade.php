<x-layouts.app title="Dashboard · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard')]">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Esto es lo que está pasando en tu catálogo hoy, {{ now()->translatedFormat('d \d\e F \d\e Y') }}.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([
            ['label' => 'Productos totales', 'value' => $metrics['total'], 'route' => route('catalog.products.index')],
            ['label' => 'Activos', 'value' => $metrics['activos'], 'route' => route('catalog.products.index', ['status' => 'activo'])],
            ['label' => 'Borradores', 'value' => $metrics['borradores'], 'route' => route('catalog.products.index', ['status' => 'borrador'])],
            ['label' => 'Sin imagen', 'value' => $metrics['sin_imagen'], 'route' => route('catalog.products.index')],
            ['label' => 'Colecciones', 'value' => $metrics['colecciones'], 'route' => route('catalog.collections.index')],
            ['label' => 'Destacados', 'value' => $metrics['destacados'], 'route' => route('catalog.products.index', ['featured' => 1])],
        ] as $card)
            <a href="{{ $card['route'] }}" class="nodo-card p-4 transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900 dark:text-white">{{ $card['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="nodo-card p-6 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Crecimiento del catálogo (últimos 6 meses)</h2>
            </div>
            @if ($growth->isEmpty())
                <p class="py-8 text-center text-sm text-slate-400">Todavía no hay suficiente historial para graficar.</p>
            @else
                <div class="flex h-40 items-end gap-3">
                    @php $max = max($growth->max(), 1); @endphp
                    @foreach ($growth as $periodo => $total)
                        <div class="flex flex-1 flex-col items-center gap-2">
                            <div class="w-full rounded-t-lg bg-gradient-to-t from-blue-600 to-violet-500" style="height: {{ max(8, ($total / $max) * 100) }}%"></div>
                            <span class="text-[11px] text-slate-400">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $periodo)->translatedFormat('M') }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="nodo-card p-6">
            <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Productos por colección</h2>
            @forelse ($byCollection as $col)
                <div class="mb-3 flex items-center justify-between text-sm">
                    <span class="truncate text-slate-600 dark:text-slate-300">{{ $col->name }}</span>
                    <span class="nodo-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $col->products_count }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Sin colecciones todavía.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="nodo-card p-6 lg:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Actividad reciente</h2>
                @can('ver actividad')
                    <a href="{{ route('admin.activity.index') }}" class="text-xs font-medium text-blue-600 hover:underline">Ver todo</a>
                @endcan
            </div>
            @forelse ($recentActivity as $log)
                <div class="flex items-start gap-3 border-b border-slate-100 py-3 text-sm last:border-0 dark:border-slate-800">
                    <div class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="text-slate-700 dark:text-slate-200">{{ $log->description }} @if($log->causer) — <span class="text-slate-400">{{ $log->causer->name }}</span>@endif</p>
                        <p class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-400">Sin actividad reciente.</p>
            @endforelse
        </div>

        <div class="nodo-card p-6">
            <h2 class="mb-4 text-sm font-semibold text-slate-900 dark:text-white">Últimos productos</h2>
            @forelse ($recentProducts as $p)
                <a href="{{ route('catalog.products.edit', $p) }}" class="mb-3 flex items-center justify-between rounded-lg px-2 py-1.5 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                    <span class="truncate text-slate-700 dark:text-slate-200">{{ $p->name }}</span>
                    <span class="nodo-badge {{ $p->status === 'activo' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ ucfirst($p->status) }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400">Aún no hay productos.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
