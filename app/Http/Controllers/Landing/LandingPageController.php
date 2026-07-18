<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\ContactList;
use App\Models\LandingPage;
use App\Models\Product;
use App\Services\Images\QrCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver landing');

        $landings = LandingPage::query()
            ->with('product')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('landing.index', ['landings' => $landings, 'statuses' => LandingPage::STATUSES]);
    }

    public function create(): View
    {
        $this->authorize('crear landing');

        return view('landing.form', [
            'landing' => new LandingPage,
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'lists' => ContactList::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear landing');

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'borrador';

        if ($request->hasFile('hero_image')) {
            $data['hero_image_path'] = $request->file('hero_image')->store('landing', 'public');
        }
        if ($request->hasFile('og_image')) {
            $data['og_image_path'] = $request->file('og_image')->store('landing', 'public');
        }

        $landing = LandingPage::create($data);

        return redirect()->route('landing.edit', $landing)->with('success', 'Landing page creada correctamente.');
    }

    public function edit(LandingPage $landing): View
    {
        $this->authorize('editar landing');

        return view('landing.form', [
            'landing' => $landing,
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'lists' => ContactList::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, LandingPage $landing): RedirectResponse
    {
        $this->authorize('editar landing');

        $data = $this->validated($request, $landing->id);

        if ($request->hasFile('hero_image')) {
            $data['hero_image_path'] = $request->file('hero_image')->store('landing', 'public');
        }
        if ($request->hasFile('og_image')) {
            $data['og_image_path'] = $request->file('og_image')->store('landing', 'public');
        }

        $landing->update($data);

        return redirect()->route('landing.edit', $landing)->with('success', 'Landing page actualizada correctamente.');
    }

    public function destroy(LandingPage $landing): RedirectResponse
    {
        $this->authorize('eliminar landing');

        $landing->delete();

        return redirect()->route('landing.index')->with('success', 'Landing page eliminada.');
    }

    public function duplicate(LandingPage $landing): RedirectResponse
    {
        $this->authorize('crear landing');

        $copy = $landing->replicate(['slug', 'views_count', 'leads_count', 'published_at']);
        $copy->name = $landing->name.' (copia)';
        $copy->slug = LandingPage::uniqueSlug(Str::slug($copy->name));
        $copy->status = 'borrador';
        $copy->views_count = 0;
        $copy->leads_count = 0;
        $copy->published_at = null;
        $copy->save();

        return redirect()->route('landing.edit', $copy)->with('success', 'Landing page duplicada correctamente.');
    }

    public function publish(LandingPage $landing): RedirectResponse
    {
        $this->authorize('publicar landing');

        if (! $landing->headline || empty($landing->sections)) {
            return back()->with('error', 'Agrega al menos un titular y una sección de contenido antes de publicar.');
        }

        $landing->update(['status' => 'publicada', 'published_at' => now()]);

        return back()->with('success', 'Landing page publicada. Ya está disponible en '.$landing->publicUrl());
    }

    public function unpublish(LandingPage $landing): RedirectResponse
    {
        $this->authorize('publicar landing');

        $landing->update(['status' => 'borrador']);

        return back()->with('success', 'Landing page despublicada.');
    }

    public function leads(LandingPage $landing): View
    {
        $this->authorize('ver landing');

        $leads = $landing->leads()->with('crmDeal')->latest()->paginate(30);

        return view('landing.leads', ['landing' => $landing, 'leads' => $leads]);
    }

    public function qrCode(LandingPage $landing, QrCodeGenerator $generator): Response
    {
        $this->authorize('ver landing');

        $png = $generator->make($landing->publicUrl(), 400)->toPng();

        return response($png, 200)->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="qr-'.$landing->slug.'.png"');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'product_id' => ['nullable', 'exists:products,id'],
            'headline' => ['required', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string', 'max:500'],
            'sections' => ['nullable', 'string'],
            'cta_text' => ['required', 'string', 'max:100'],
            'cta_whatsapp_number' => ['nullable', 'string', 'max:30'],
            'cta_whatsapp_message' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'url', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'ga4_id' => ['nullable', 'string', 'max:50'],
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
            'gtm_id' => ['nullable', 'string', 'max:50'],
            'capture_form_enabled' => ['nullable', 'boolean'],
            'contact_list_id' => ['nullable', 'exists:contact_lists,id'],
            'hero_image' => ['nullable', 'image', 'max:4096'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['sections'] = $data['sections'] ? json_decode($data['sections'], true) : [];
        $data['capture_form_enabled'] = $request->boolean('capture_form_enabled', true);

        unset($data['hero_image'], $data['og_image']);

        return $data;
    }
}
