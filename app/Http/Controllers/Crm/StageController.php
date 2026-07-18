<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StageController extends Controller
{
    public function index(): View
    {
        $this->authorize('ver crm');

        return view('crm.stages.index', ['stages' => CrmStage::orderBy('sort_order')->withCount('deals')->get()]);
    }

    public function create(): View
    {
        $this->authorize('crear crm');

        return view('crm.stages.form', ['stage' => new CrmStage]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear crm');

        CrmStage::create($this->validated($request));

        return redirect()->route('crm.stages.index')->with('success', 'Etapa creada correctamente.');
    }

    public function edit(CrmStage $stage): View
    {
        $this->authorize('editar crm');

        return view('crm.stages.form', ['stage' => $stage]);
    }

    public function update(Request $request, CrmStage $stage): RedirectResponse
    {
        $this->authorize('editar crm');

        $stage->update($this->validated($request));

        return redirect()->route('crm.stages.index')->with('success', 'Etapa actualizada correctamente.');
    }

    public function destroy(CrmStage $stage): RedirectResponse
    {
        $this->authorize('eliminar crm');

        if ($stage->deals()->exists()) {
            return back()->with('error', 'No puedes eliminar una etapa que tiene prospectos asignados. Muévelos primero a otra etapa.');
        }

        $stage->delete();

        return redirect()->route('crm.stages.index')->with('success', 'Etapa eliminada.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_won' => ['nullable', 'boolean'],
            'is_lost' => ['nullable', 'boolean'],
        ]);

        $data['is_won'] = $request->boolean('is_won');
        $data['is_lost'] = $request->boolean('is_lost');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
