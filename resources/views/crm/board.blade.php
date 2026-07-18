@php
    $dealsByStageJson = [];
    foreach ($stages as $stage) {
        $dealsByStageJson[$stage->id] = collect($deals->get($stage->id, collect()))->map(fn ($deal) => [
            'id' => $deal->id,
            'title' => $deal->title,
            'contact_name' => $deal->contact?->name ?: $deal->contact?->email,
            'value' => $deal->formattedValue(),
            'product_name' => $deal->product?->name,
            'assignee_name' => $deal->assignee?->name,
            'edit_url' => route('crm.edit', $deal),
        ])->values()->all();
    }
@endphp
<x-layouts.app title="CRM · NODO Catalog Manager" :breadcrumbs="['Dashboard' => route('dashboard'), 'CRM' => '']">
    <div x-data="crmBoard(@js($dealsByStageJson), @js(csrf_token()))">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-slate-900 dark:text-white">Pipeline de prospectos</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Arrastra las tarjetas entre columnas para cambiar de etapa.</p>
            </div>
            <div class="flex gap-2">
                @can('editar crm')
                    <a href="{{ route('crm.stages.index') }}" class="nodo-btn-secondary">Etapas</a>
                @endcan
                @can('crear crm')
                    <a href="{{ route('crm.create') }}" class="nodo-btn-primary">+ Nuevo prospecto</a>
                @endcan
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">{{ session('success') }}</div>
        @endif

        <div class="flex gap-4 overflow-x-auto pb-4">
            @foreach ($stages as $stage)
                <div class="w-72 shrink-0 rounded-xl bg-slate-50 dark:bg-slate-900/50"
                     @dragover.prevent
                     @drop.prevent="onDrop({{ $stage->id }})">
                    <div class="flex items-center justify-between px-3 py-3">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $stage->color }}"></span>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $stage->name }}</span>
                        </div>
                        <span class="text-xs text-slate-400" x-text="stageDeals({{ $stage->id }}).length"></span>
                    </div>

                    <div class="min-h-[80px] space-y-2 px-3 pb-3">
                        <template x-for="deal in stageDeals({{ $stage->id }})" :key="deal.id">
                            <a :href="deal.edit_url"
                               draggable="true"
                               @dragstart="onDragStart($event, deal.id, {{ $stage->id }})"
                               class="block cursor-move rounded-lg border border-slate-200 bg-white p-3 shadow-sm hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900">
                                <p class="text-sm font-medium text-slate-900 dark:text-white" x-text="deal.title"></p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-text="deal.contact_name"></p>
                                <div class="mt-2 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300" x-text="deal.value"></span>
                                    <span x-show="deal.assignee_name" x-text="deal.assignee_name" class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-500 dark:bg-slate-800"></span>
                                </div>
                            </a>
                        </template>
                        <template x-if="stageDeals({{ $stage->id }}).length === 0">
                            <p class="rounded-lg border border-dashed border-slate-200 py-6 text-center text-xs text-slate-400 dark:border-slate-800">Sin prospectos</p>
                        </template>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function crmBoard(initialDealsByStage, csrfToken) {
            return {
                dealsByStage: initialDealsByStage,
                dragDealId: null,
                dragFromStage: null,
                stageDeals(stageId) {
                    return this.dealsByStage[stageId] || [];
                },
                onDragStart(event, dealId, stageId) {
                    this.dragDealId = dealId;
                    this.dragFromStage = stageId;
                    event.dataTransfer.effectAllowed = 'move';
                },
                onDrop(toStageId) {
                    if (! this.dragDealId || this.dragFromStage === toStageId) return;

                    const fromList = this.dealsByStage[this.dragFromStage] || [];
                    const idx = fromList.findIndex(d => d.id === this.dragDealId);
                    if (idx === -1) return;
                    const [deal] = fromList.splice(idx, 1);

                    if (! this.dealsByStage[toStageId]) this.dealsByStage[toStageId] = [];
                    this.dealsByStage[toStageId].push(deal);

                    fetch(`/crm/${deal.id}/mover`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ stage_id: toStageId }),
                    });

                    this.dragDealId = null;
                    this.dragFromStage = null;
                },
            };
        }
    </script>
</x-layouts.app>
