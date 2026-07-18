<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommerceSyncLog;
use App\Models\Setting;
use App\Services\Commerce\CommerceFeedConfig;
use App\Services\Commerce\MetaCatalogClient;
use App\Services\Commerce\MetaCommerceException;
use App\Services\Commerce\ProductFeedGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommerceSettingsController extends Controller
{
    public function edit(CommerceFeedConfig $config, ProductFeedGenerator $generator): View
    {
        $this->authorize('ver comercio');

        return view('admin.commerce.edit', [
            'catalogId' => $config->metaCatalogId(),
            'businessId' => $config->metaBusinessId(),
            'hasToken' => ! empty($config->metaAccessToken()),
            'isConfigured' => $config->isMetaConfigured(),
            'feedToken' => $config->feedToken(),
            'eligibleCount' => $generator->eligibleProducts()->count(),
            'totalActive' => \App\Models\Product::where('status', 'activo')->count(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('configurar comercio');

        $data = $request->validate([
            'meta_catalog_id' => ['nullable', 'string', 'max:255'],
            'meta_business_id' => ['nullable', 'string', 'max:255'],
            'meta_access_token' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::set('meta_catalog_id', $data['meta_catalog_id'] ?? '', 'commerce');
        Setting::set('meta_business_id', $data['meta_business_id'] ?? '', 'commerce');

        if (! empty($data['meta_access_token'])) {
            Setting::set('meta_access_token', $data['meta_access_token'], 'commerce', encrypted: true);
        }

        activity('configuracion')->causedBy($request->user())->log('Actualizó la configuración de Meta Commerce');

        return back()->with('success', 'Configuración de Meta Commerce actualizada.');
    }

    public function test(CommerceFeedConfig $config, MetaCatalogClient $client): JsonResponse
    {
        $this->authorize('configurar comercio');

        if (! $config->isMetaConfigured()) {
            return response()->json(['ok' => false, 'message' => 'Configura el ID de catálogo y el token de acceso antes de probar.'], 422);
        }

        try {
            $result = $client->verifyCatalog($config->metaCatalogId(), $config->metaAccessToken());

            CommerceSyncLog::create([
                'type' => 'connection_test', 'status' => 'exitoso',
                'message' => "Conexión verificada con el catálogo \"{$result['name']}\" ({$result['product_count']} productos en Meta).",
            ]);

            return response()->json(['ok' => true, 'message' => "Conexión exitosa. Catálogo: \"{$result['name']}\" — {$result['product_count']} producto(s) en Meta."]);
        } catch (MetaCommerceException $e) {
            CommerceSyncLog::create(['type' => 'connection_test', 'status' => 'error', 'message' => $e->getMessage()]);

            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function regenerateToken(Request $request, CommerceFeedConfig $config): RedirectResponse
    {
        $this->authorize('configurar comercio');

        $config->regenerateFeedToken();

        activity('configuracion')->causedBy($request->user())->log('Regeneró el token del feed de catálogo');

        return back()->with('success', 'Se generó un nuevo enlace de feed. Actualiza la URL en Meta Commerce Manager.');
    }

    public function history(): View
    {
        $this->authorize('ver comercio');

        $logs = CommerceSyncLog::latest('created_at')->paginate(30);

        return view('admin.commerce.history', ['logs' => $logs]);
    }
}
