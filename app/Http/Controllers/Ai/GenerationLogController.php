<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Models\AiGeneration;
use App\Services\AI\ContentGenerationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenerationLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver historial ia');

        $generations = AiGeneration::query()
            ->with(['user', 'product'])
            ->when($request->task, fn ($q) => $q->where('task', $request->task))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('ai.history', [
            'generations' => $generations,
            'tasks' => ContentGenerationService::TASKS,
        ]);
    }
}
