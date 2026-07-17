<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = Collection::withCount('products')->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('catalog.collections.index', ['collections' => $collections]);
    }

    public function create(): View
    {
        $this->authorize('crear colecciones');

        return view('catalog.collections.form', ['collection' => new Collection]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear colecciones');

        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        Collection::create($data);

        return redirect()->route('catalog.collections.index')->with('success', 'Colección creada correctamente.');
    }

    public function edit(Collection $collection): View
    {
        $this->authorize('editar colecciones');

        return view('catalog.collections.form', ['collection' => $collection]);
    }

    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $this->authorize('editar colecciones');

        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        $collection->update($data);

        return redirect()->route('catalog.collections.index')->with('success', 'Colección actualizada correctamente.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $this->authorize('eliminar colecciones');

        if ($collection->products()->exists()) {
            return back()->with('error', 'No puedes eliminar una colección que tiene productos asociados.');
        }

        $collection->delete();

        return back()->with('success', 'Colección eliminada correctamente.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:7'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
