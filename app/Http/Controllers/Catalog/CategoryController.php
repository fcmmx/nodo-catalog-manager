<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->with('collection')->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('catalog.categories.index', ['categories' => $categories]);
    }

    public function create(): View
    {
        $this->authorize('crear categorias');

        return view('catalog.categories.form', ['category' => new Category, 'collections' => Collection::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear categorias');

        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        Category::create($data);

        return redirect()->route('catalog.categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Category $category): View
    {
        $this->authorize('editar categorias');

        return view('catalog.categories.form', ['category' => $category, 'collections' => Collection::orderBy('name')->get()]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('editar categorias');

        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);

        return redirect()->route('catalog.categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('eliminar categorias');

        if ($category->products()->exists()) {
            return back()->with('error', 'No puedes eliminar una categoría que tiene productos asociados.');
        }

        $category->delete();

        return back()->with('success', 'Categoría eliminada correctamente.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'collection_id' => ['nullable', 'exists:collections,id'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
