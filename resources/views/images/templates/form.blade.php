<x-layouts.app :title="($template->exists ? 'Editar' : 'Nueva').' plantilla de imagen · NODO Catalog Manager'" :breadcrumbs="['Dashboard' => route('dashboard'), 'Imágenes' => route('images.generator'), 'Plantillas' => route('images.templates.index'), ($template->exists ? 'Editar' : 'Nueva') => '']">
    <div class="mx-auto max-w-2xl">
        <div class="nodo-card p-6">
            <h1 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">{{ $template->exists ? 'Editar plantilla' : 'Nueva plantilla' }}</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-950 dark:text-red-300">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $template->exists ? route('images.templates.update', $template) : route('images.templates.store') }}" class="space-y-5">
                @csrf
                @if ($template->exists) @method('PUT') @endif

                <div>
                    <label class="nodo-label">Nombre</label>
                    <input name="name" value="{{ old('name', $template->name) }}" required class="nodo-input">
                </div>

                <div>
                    <label class="nodo-label">Formato</label>
                    <select name="format" class="nodo-input" {{ $template->exists ? 'disabled' : '' }}>
                        @foreach (\App\Models\ImageTemplate::FORMATS as $key => $format)
                            <option value="{{ $key }}" {{ old('format', $template->format) === $key ? 'selected' : '' }}>{{ $format['label'] }}</option>
                        @endforeach
                    </select>
                    @if ($template->exists)
                        <input type="hidden" name="format" value="{{ $template->format }}">
                        <p class="mt-1 text-xs text-slate-400">El formato no se puede cambiar después de crear la plantilla.</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Tipo de fondo</label>
                        <select name="background_type" class="nodo-input">
                            <option value="color" {{ old('background_type', $template->background_type ?? 'color') === 'color' ? 'selected' : '' }}>Color / degradado</option>
                            <option value="image" {{ old('background_type', $template->background_type) === 'image' ? 'selected' : '' }}>Imagen subida al generar</option>
                            <option value="ai" {{ old('background_type', $template->background_type) === 'ai' ? 'selected' : '' }}>Generado con IA al generar</option>
                        </select>
                    </div>
                    <div>
                        <label class="nodo-label">Color base del degradado</label>
                        <input type="color" name="background_value" value="{{ old('background_value', $template->background_value ?? '#0F172A') }}" class="nodo-input h-10 p-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="nodo-label">Color primario (degradado / CTA)</label>
                        <input type="color" name="primary_color" value="{{ old('primary_color', $template->primary_color ?? '#2563EB') }}" class="nodo-input h-10 p-1">
                    </div>
                    <div>
                        <label class="nodo-label">Color de acento (precio)</label>
                        <input type="color" name="accent_color" value="{{ old('accent_color', $template->accent_color ?? '#DC2626') }}" class="nodo-input h-10 p-1">
                    </div>
                </div>

                <div>
                    <label class="nodo-label">Posición del título</label>
                    <select name="title_position" class="nodo-input">
                        <option value="top" {{ old('title_position', $template->title_position) === 'top' ? 'selected' : '' }}>Arriba</option>
                        <option value="center" {{ old('title_position', $template->title_position ?? 'center') === 'center' ? 'selected' : '' }}>Centro</option>
                        <option value="bottom" {{ old('title_position', $template->title_position) === 'bottom' ? 'selected' : '' }}>Abajo</option>
                    </select>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="overlay_gradient" value="1" {{ old('overlay_gradient', $template->overlay_gradient ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Degradado oscuro para legibilidad del texto
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="show_price" value="1" {{ old('show_price', $template->show_price) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Mostrar precio
                    </label>
                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="show_qr" value="1" {{ old('show_qr', $template->show_qr) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                        Mostrar código QR
                    </label>
                </div>

                <div>
                    <label class="nodo-label">Pie de marca</label>
                    <input name="footer_text" value="{{ old('footer_text', $template->footer_text) }}" class="nodo-input" placeholder="NODO 360 Marketing Technology">
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('images.templates.index') }}" class="nodo-btn-secondary">Cancelar</a>
                    <button type="submit" class="nodo-btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
