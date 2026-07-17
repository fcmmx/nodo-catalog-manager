<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Models\ImageTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(): View
    {
        $this->authorize('ver imagenes');

        $templates = ImageTemplate::withCount('generations')->orderByDesc('is_master')->orderBy('name')->get();

        return view('images.templates.index', ['templates' => $templates]);
    }

    public function create(): View
    {
        $this->authorize('crear imagenes');

        return view('images.templates.form', ['template' => new ImageTemplate]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear imagenes');

        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['created_by'] = $request->user()->id;

        $template = ImageTemplate::create($data);

        return redirect()->route('images.templates.edit', $template)->with('success', 'Plantilla creada correctamente.');
    }

    public function edit(ImageTemplate $template): View
    {
        $this->authorize('editar imagenes');

        return view('images.templates.form', ['template' => $template]);
    }

    public function update(Request $request, ImageTemplate $template): RedirectResponse
    {
        $this->authorize('editar imagenes');

        $data = $this->validated($request);

        if ($data['name'] !== $template->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $template->id);
        }

        $template->update($data);

        return redirect()->route('images.templates.edit', $template)->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(ImageTemplate $template): RedirectResponse
    {
        $this->authorize('eliminar imagenes');

        if ($template->is_master) {
            return back()->with('error', 'No puedes eliminar la plantilla maestra de NODO 360.');
        }

        $template->delete();

        return back()->with('success', 'Plantilla eliminada correctamente.');
    }

    protected function validated(Request $request): array
    {
        $format = $request->string('format')->toString();
        $preset = ImageTemplate::FORMATS[$format] ?? null;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'format' => ['required', 'in:'.implode(',', array_keys(ImageTemplate::FORMATS))],
            'background_type' => ['required', 'in:color,image,ai'],
            'background_value' => ['nullable', 'string', 'max:255'],
            'overlay_gradient' => ['nullable', 'boolean'],
            'primary_color' => ['required', 'string', 'max:20'],
            'accent_color' => ['required', 'string', 'max:20'],
            'title_position' => ['required', 'in:top,center,bottom'],
            'show_price' => ['nullable', 'boolean'],
            'show_qr' => ['nullable', 'boolean'],
            'footer_text' => ['nullable', 'string', 'max:255'],
        ]);

        $data['overlay_gradient'] = $request->boolean('overlay_gradient');
        $data['show_price'] = $request->boolean('show_price');
        $data['show_qr'] = $request->boolean('show_qr');
        $data['width'] = $preset['width'];
        $data['height'] = $preset['height'];

        return $data;
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (ImageTemplate::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
