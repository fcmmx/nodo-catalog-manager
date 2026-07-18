<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use App\Models\CrmDeal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request, CrmDeal $deal): RedirectResponse
    {
        $this->authorize('editar crm');

        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', array_keys(CrmActivity::TYPES))],
            'content' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['nullable', 'date'],
        ]);

        $deal->activities()->create([
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'content' => $data['content'] ?? null,
            'due_at' => $data['due_at'] ?? null,
        ]);

        return back()->with('success', 'Actividad registrada correctamente.');
    }

    public function complete(CrmActivity $activity): RedirectResponse
    {
        $this->authorize('editar crm');

        $activity->update(['completed_at' => now()]);

        return back()->with('success', 'Recordatorio marcado como completado.');
    }

    public function destroy(CrmActivity $activity): RedirectResponse
    {
        $this->authorize('editar crm');

        $activity->delete();

        return back()->with('success', 'Actividad eliminada.');
    }
}
