<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AI\AiClientFactory;
use App\Services\AI\AiConfig;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiSettingsController extends Controller
{
    public function edit(AiConfig $config): View
    {
        $this->authorize('configurar ia');

        return view('admin.ai.edit', [
            'enabled' => $config->enabled(),
            'provider' => $config->provider(),
            'model' => $config->model(),
            'baseUrl' => $config->baseUrl(),
            'maskedKey' => $config->maskedApiKey(),
            'isConfigured' => $config->isConfigured(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('configurar ia');

        $data = $request->validate([
            'ai_enabled' => ['nullable', 'boolean'],
            'ai_provider' => ['required', 'in:openai,google'],
            'ai_model' => ['required', 'string', 'max:100'],
            'ai_base_url' => ['required', 'url', 'max:255'],
            'ai_api_key' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('ai_enabled', $request->boolean('ai_enabled') ? '1' : '0', 'ai');
        Setting::set('ai_provider', $data['ai_provider'], 'ai');
        Setting::set('ai_model', $data['ai_model'], 'ai');
        Setting::set('ai_base_url', $data['ai_base_url'], 'ai');

        if (! empty($data['ai_api_key'])) {
            Setting::set('ai_api_key', $data['ai_api_key'], 'ai', encrypted: true);
        }

        activity('configuracion')->causedBy($request->user())->log('Actualizó la configuración de inteligencia artificial');

        return back()->with('success', 'Configuración de IA actualizada correctamente.');
    }

    public function test(Request $request, AiClientFactory $factory): JsonResponse
    {
        $this->authorize('configurar ia');

        try {
            $client = $factory->make();
            $result = $client->complete(
                'Responde únicamente con la palabra: OK',
                'Confirma que la conexión funciona.'
            );

            return response()->json(['ok' => true, 'message' => 'Conexión exitosa. Respuesta del proveedor: '.$result->content]);
        } catch (AiException $e) {
            return response()->json(['ok' => false, 'reason' => $e->reason, 'message' => $e->getMessage()], 422);
        }
    }
}
