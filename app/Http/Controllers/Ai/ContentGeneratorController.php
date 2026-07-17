<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiGeneration;
use App\Models\Product;
use App\Services\AI\AiConfig;
use App\Services\AI\ContentGenerationService;
use App\Services\AI\Exceptions\AiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentGeneratorController extends Controller
{
    public function index(AiConfig $config): View
    {
        $this->authorize('usar ia');

        return view('ai.generator', [
            'tasks' => ContentGenerationService::TASKS,
            'isConfigured' => $config->isConfigured(),
        ]);
    }

    public function generate(Request $request, ContentGenerationService $service): JsonResponse
    {
        $this->authorize('usar ia');

        $data = $request->validate([
            'task' => ['required', 'string', 'in:'.implode(',', array_keys(ContentGenerationService::TASKS))],
            'tema' => ['nullable', 'string', 'max:2000'],
            'texto' => ['nullable', 'string', 'max:5000'],
            'tono' => ['nullable', 'string', 'max:100'],
            'idioma' => ['nullable', 'string', 'max:50'],
            'canal' => ['nullable', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
        ]);

        $product = ! empty($data['product_id']) ? Product::find($data['product_id']) : null;

        try {
            $generation = $service->generate($request->user(), $data['task'], $data, $product);

            return response()->json([
                'ok' => true,
                'id' => $generation->id,
                'content' => $generation->response,
                'tokens' => [
                    'input' => $generation->input_tokens,
                    'output' => $generation->output_tokens,
                ],
                'estimated_cost' => $generation->estimated_cost,
            ]);
        } catch (AiException $e) {
            return response()->json(['ok' => false, 'reason' => $e->reason, 'message' => $e->getMessage()], 422);
        }
    }

    public function approve(AiGeneration $generation): RedirectResponse
    {
        $this->authorize('usar ia');

        $generation->update(['status' => 'aprobado']);

        return back()->with('success', 'Contenido generado marcado como aprobado.');
    }

    public function reject(AiGeneration $generation): RedirectResponse
    {
        $this->authorize('usar ia');

        $generation->update(['status' => 'rechazado']);

        return back()->with('success', 'Contenido generado marcado como rechazado.');
    }
}
