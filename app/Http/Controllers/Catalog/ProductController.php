<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver productos');

        $products = Product::query()
            ->with(['category', 'collection'])
            ->search($request->q)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->collection_id, fn ($q) => $q->where('collection_id', $request->collection_id))
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->featured, fn ($q) => $q->where('is_featured', true))
            ->when($request->trashed === '1', fn ($q) => $q->onlyTrashed())
            ->orderBy($request->sort ?? 'sort_order')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('catalog.products.index', [
            'products' => $products,
            'collections' => Collection::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('crear productos');

        return view('catalog.products.form', [
            'product' => new Product,
            'collections' => Collection::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear productos');

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product = Product::create($data);
        $this->syncGallery($request, $product);

        return redirect()->route('catalog.products.edit', $product)->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('ver productos');

        return view('catalog.products.form', [
            'product' => $product->load('images'),
            'collections' => Collection::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('editar productos');

        $data = $this->validated($request, $product->id);
        $data['updated_by'] = $request->user()->id;

        if ($request->hasFile('main_image')) {
            if ($product->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);
        $this->syncGallery($request, $product);

        return redirect()->route('catalog.products.edit', $product)->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('eliminar productos');

        $product->delete();

        return back()->with('success', 'Producto enviado a la papelera. Puedes restaurarlo cuando lo necesites.');
    }

    public function restore(int $product): RedirectResponse
    {
        $this->authorize('editar productos');

        $model = Product::onlyTrashed()->findOrFail($product);
        $model->restore();

        return back()->with('success', 'Producto restaurado correctamente.');
    }

    public function duplicate(Product $product): RedirectResponse
    {
        $this->authorize('crear productos');

        $copy = $product->duplicate();

        return redirect()->route('catalog.products.edit', $copy)->with('success', 'Producto duplicado. Revisa el nuevo borrador.');
    }

    public function archive(Product $product): RedirectResponse
    {
        $this->authorize('editar productos');

        $product->update(['status' => $product->status === 'archivado' ? 'borrador' : 'archivado']);

        return back()->with('success', $product->status === 'archivado' ? 'Producto archivado.' : 'Producto reactivado.');
    }

    public function preview(Product $product): View
    {
        $this->authorize('ver productos');

        return view('catalog.products.preview', ['product' => $product->load('images', 'category', 'collection')]);
    }

    public function bulk(Request $request): RedirectResponse
    {
        $this->authorize('editar productos');

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:products,id'],
            'action' => ['required', 'in:price,category,collection,status,tags,availability,published_at,featured,delete'],
            'value' => ['nullable'],
        ]);

        $query = Product::whereIn('id', $data['ids']);

        switch ($data['action']) {
            case 'price':
                $query->update(['price' => $request->input('value')]);
                break;
            case 'category':
                $query->update(['category_id' => $request->input('value') ?: null]);
                break;
            case 'collection':
                $query->update(['collection_id' => $request->input('value') ?: null]);
                break;
            case 'status':
                $query->update(['status' => $request->input('value')]);
                break;
            case 'availability':
                $query->update(['availability' => $request->input('value')]);
                break;
            case 'featured':
                $query->update(['is_featured' => $request->boolean('value')]);
                break;
            case 'published_at':
                $query->update(['published_at' => $request->input('value') ?: null]);
                break;
            case 'tags':
                foreach ($query->get() as $product) {
                    $product->update(['tags' => array_filter(array_map('trim', explode(',', (string) $request->input('value'))))]);
                }
                break;
            case 'delete':
                $query->delete();
                break;
        }

        activity('catalogo')->causedBy($request->user())->log("Edición masiva ({$data['action']}) sobre ".count($data['ids']).' producto(s)');

        return back()->with('success', 'Cambios aplicados a '.count($data['ids']).' producto(s).');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('exportar productos');

        $format = $request->get('format', 'csv');
        $products = Product::query()
            ->with(['category', 'collection'])
            ->search($request->q)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->collection_id, fn ($q) => $q->where('collection_id', $request->collection_id))
            ->when($request->ids, fn ($q) => $q->whereIn('id', explode(',', $request->ids)))
            ->get();

        activity('catalogo')->causedBy($request->user())->log('Exportó '.$products->count()." producto(s) en formato {$format}");

        if ($format === 'json') {
            return response()->stream(function () use ($products) {
                echo $products->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="catalogo-nodo360-'.now()->format('Ymd-His').'.json"',
            ]);
        }

        return $this->streamCsv($products);
    }

    protected function streamCsv($products): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="catalogo-nodo360-'.now()->format('Ymd-His').'.csv"',
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['sku', 'nombre', 'coleccion', 'categoria', 'tipo', 'precio', 'moneda', 'estado', 'disponibilidad', 'url']);
            foreach ($products as $p) {
                fputcsv($handle, [
                    $p->sku, $p->name, $p->collection?->name, $p->category?->name, $p->type,
                    $p->price, $p->currency, $p->status, $p->availability, $p->url,
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    protected function syncGallery(Request $request, Product $product): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $nextOrder = $product->images()->max('sort_order') + 1;

        foreach ($request->file('gallery') as $file) {
            $path = $file->store('products/gallery', 'public');
            $product->images()->create([
                'path' => $path,
                'sort_order' => $nextOrder++,
            ]);
        }
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.($ignoreId ?? 'NULL').',id'],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'collection_id' => ['nullable', 'exists:collections,id'],
            'type' => ['required', 'in:producto,servicio'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'pricing_model' => ['nullable', 'string', 'max:50'],
            'price_prefix_text' => ['nullable', 'string', 'max:50'],
            'availability' => ['required', 'in:disponible,agotado,bajo_pedido,proximamente'],
            'status' => ['required', 'in:borrador,activo,inactivo,archivado'],
            'main_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'gallery.*' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'url' => ['nullable', 'url', 'max:500'],
            'demo_url' => ['nullable', 'url', 'max:500'],
            'whatsapp_url' => ['nullable', 'url', 'max:500'],
            'whatsapp_message' => ['nullable', 'string', 'max:1000'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'seo_text' => ['nullable', 'string', 'max:1000'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $data['tax_included'] = $request->boolean('tax_included');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['tags'] = $request->filled('tags')
            ? array_values(array_filter(array_map('trim', explode(',', $request->string('tags')))))
            : [];

        if (empty($data['slug'])) {
            unset($data['slug']);
        } else {
            $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);
        }

        return $data;
    }
}
