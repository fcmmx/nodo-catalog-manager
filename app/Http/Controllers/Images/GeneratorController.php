<?php

namespace App\Http\Controllers\Images;

use App\Http\Controllers\Controller;
use App\Models\ImageGeneration;
use App\Models\ImageTemplate;
use App\Models\Product;
use App\Services\AI\Exceptions\AiException;
use App\Services\Images\AiImageService;
use App\Services\Images\ImageGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GeneratorController extends Controller
{
    public function index(Request $request, AiImageService $aiImageService): View
    {
        $this->authorize('ver imagenes');

        return view('images.generator.create', [
            'templates' => ImageTemplate::orderByDesc('is_master')->orderBy('name')->get(),
            'products' => Product::orderBy('name')->limit(500)->get(['id', 'name', 'main_image', 'url', 'whatsapp_url']),
            'selectedProduct' => $request->integer('product_id') ? Product::find($request->integer('product_id')) : null,
            'aiImagesAvailable' => $aiImageService->available(),
        ]);
    }

    public function store(Request $request, ImageGenerationService $service): RedirectResponse
    {
        $this->authorize('crear imagenes');

        $data = $request->validate([
            'template_id' => ['required', 'exists:image_templates,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'title' => ['nullable', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'cta_text' => ['nullable', 'string', 'max:60'],
            'price_text' => ['nullable', 'string', 'max:60'],
            'qr_target_url' => ['nullable', 'url', 'max:255'],
            'background_source' => ['required', 'in:color,upload,product_image,ai'],
            'background_upload' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:8192'],
            'ai_prompt' => ['nullable', 'string', 'max:1000'],
        ]);

        $template = ImageTemplate::findOrFail($data['template_id']);
        $product = ! empty($data['product_id']) ? Product::find($data['product_id']) : null;

        try {
            $generation = $service->generate(
                user: $request->user(),
                template: $template,
                inputs: $data,
                backgroundSource: $data['background_source'],
                upload: $request->file('background_upload'),
                product: $product,
            );
        } catch (AiException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        activity('imagenes')->causedBy($request->user())->log('Generó una imagen con la plantilla '.$template->name);

        return redirect()->route('images.generations.show', $generation)->with('success', 'Imagen generada correctamente.');
    }

    public function show(ImageGeneration $generation): View
    {
        $this->authorize('ver imagenes');

        return view('images.generator.show', ['generation' => $generation->load('template', 'product')]);
    }

    public function history(Request $request): View
    {
        $this->authorize('ver imagenes');

        $generations = ImageGeneration::with(['template', 'product', 'user'])
            ->when($request->product_id, fn ($q) => $q->where('product_id', $request->product_id))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        return view('images.generator.history', ['generations' => $generations]);
    }

    public function useAsMainImage(ImageGeneration $generation): RedirectResponse
    {
        $this->authorize('editar imagenes');

        if (! $generation->product_id || ! $generation->file_path) {
            return back()->with('error', 'Esta imagen no está asociada a un producto o no tiene archivo generado.');
        }

        $generation->product->update(['main_image' => $generation->file_path]);

        activity('imagenes')->log('Estableció una imagen generada como principal del producto '.$generation->product->name);

        return back()->with('success', 'Imagen establecida como principal del producto.');
    }

    public function addToGallery(ImageGeneration $generation): RedirectResponse
    {
        $this->authorize('editar imagenes');

        if (! $generation->product_id || ! $generation->file_path) {
            return back()->with('error', 'Esta imagen no está asociada a un producto o no tiene archivo generado.');
        }

        $nextOrder = $generation->product->images()->max('sort_order') + 1;

        $generation->product->images()->create([
            'path' => $generation->file_path,
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', 'Imagen agregada a la galería del producto.');
    }

    public function destroy(ImageGeneration $generation): RedirectResponse
    {
        $this->authorize('eliminar imagenes');

        if ($generation->file_path) {
            Storage::disk('public')->delete($generation->file_path);
        }

        $generation->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
