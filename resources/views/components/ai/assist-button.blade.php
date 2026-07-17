@props(['target', 'task', 'tema' => '', 'label' => 'Generar con IA'])
<div x-data="{
        open: false, loading: false, result: null, error: null,
        run() {
            this.loading = true; this.error = null; this.open = true;
            fetch('{{ route('ai.generate') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ task: '{{ $task }}', tema: {{ Js::from($tema) }} + ' ' + (document.getElementById('{{ $target }}')?.value || '') }),
            })
                .then(r => r.json().then(data => ({ status: r.status, data })))
                .then(({ status, data }) => {
                    this.loading = false;
                    if (status !== 200 || !data.ok) { this.error = data.message || 'No se pudo generar el contenido.'; return; }
                    this.result = data.content;
                })
                .catch(() => { this.loading = false; this.error = 'No se pudo contactar al servidor.'; });
        },
        use() {
            const field = document.getElementById('{{ $target }}');
            if (field) { field.value = this.result; field.dispatchEvent(new Event('input')); }
            this.open = false;
        },
     }" class="inline-block">
    <button type="button" @click="run()" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline dark:text-blue-400">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
        {{ $label }}
    </button>

    <div x-show="open" x-cloak @click.outside="open = false" class="relative z-20">
        <div class="absolute left-0 top-2 w-96 nodo-card p-4 shadow-lg">
            <p x-show="loading" class="text-sm text-slate-500">Generando…</p>
            <p x-show="error" x-cloak class="text-sm text-red-600" x-text="error"></p>
            <template x-if="result">
                <div>
                    <textarea x-model="result" rows="6" class="nodo-input text-sm"></textarea>
                    <div class="mt-2 flex gap-2">
                        <button type="button" @click="use()" class="nodo-btn-primary text-xs">Usar este texto</button>
                        <button type="button" @click="run()" class="nodo-btn-secondary text-xs">Regenerar</button>
                        <button type="button" @click="open = false" class="nodo-btn-secondary text-xs">Cerrar</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
