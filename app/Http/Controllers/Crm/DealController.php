<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\LandingLead;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver crm');

        $stages = CrmStage::orderBy('sort_order')->withCount('deals')->get();
        $deals = CrmDeal::query()
            ->with(['contact', 'assignee', 'product'])
            ->where('status', 'abierto')
            ->when($request->assigned_to, fn ($q) => $q->where('assigned_to', $request->assigned_to))
            ->get()
            ->groupBy('stage_id');

        return view('crm.board', [
            'stages' => $stages,
            'deals' => $deals,
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('crear crm');

        return view('crm.form', [
            'deal' => new CrmDeal,
            'stages' => CrmStage::orderBy('sort_order')->get(),
            'contacts' => Contact::orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear crm');

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'abierto';

        $deal = CrmDeal::create($data);

        return redirect()->route('crm.edit', $deal)->with('success', 'Prospecto creado correctamente.');
    }

    public function edit(CrmDeal $deal): View
    {
        $this->authorize('editar crm');

        $deal->load(['activities.user', 'contact', 'stage']);

        return view('crm.form', [
            'deal' => $deal,
            'stages' => CrmStage::orderBy('sort_order')->get(),
            'contacts' => Contact::orderBy('name')->get(['id', 'name', 'email']),
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, CrmDeal $deal): RedirectResponse
    {
        $this->authorize('editar crm');

        $deal->update($this->validated($request));

        return redirect()->route('crm.edit', $deal)->with('success', 'Prospecto actualizado correctamente.');
    }

    public function destroy(CrmDeal $deal): RedirectResponse
    {
        $this->authorize('eliminar crm');

        $deal->delete();

        return redirect()->route('crm.index')->with('success', 'Prospecto eliminado.');
    }

    public function moveStage(Request $request, CrmDeal $deal): JsonResponse
    {
        $this->authorize('editar crm');

        $data = $request->validate(['stage_id' => ['required', 'exists:crm_stages,id']]);
        $stage = CrmStage::findOrFail($data['stage_id']);

        $deal->update([
            'stage_id' => $stage->id,
            'status' => $stage->is_won ? 'ganado' : ($stage->is_lost ? 'perdido' : 'abierto'),
        ]);

        return response()->json(['ok' => true]);
    }

    public function markWon(CrmDeal $deal): RedirectResponse
    {
        $this->authorize('editar crm');

        $wonStage = CrmStage::where('is_won', true)->orderBy('sort_order')->first();
        $deal->update(['status' => 'ganado', 'stage_id' => $wonStage?->id ?? $deal->stage_id]);

        return back()->with('success', 'Prospecto marcado como ganado.');
    }

    public function markLost(Request $request, CrmDeal $deal): RedirectResponse
    {
        $this->authorize('editar crm');

        $data = $request->validate(['lost_reason' => ['nullable', 'string', 'max:255']]);
        $lostStage = CrmStage::where('is_lost', true)->orderBy('sort_order')->first();

        $deal->update([
            'status' => 'perdido',
            'stage_id' => $lostStage?->id ?? $deal->stage_id,
            'lost_reason' => $data['lost_reason'] ?? null,
        ]);

        return back()->with('success', 'Prospecto marcado como perdido.');
    }

    public function assign(Request $request, CrmDeal $deal): RedirectResponse
    {
        $this->authorize('asignar crm');

        $data = $request->validate(['assigned_to' => ['nullable', 'exists:users,id']]);
        $deal->update(['assigned_to' => $data['assigned_to'] ?? null]);

        return back()->with('success', 'Prospecto asignado correctamente.');
    }

    public function convertFromLead(Request $request, LandingLead $lead): RedirectResponse
    {
        $this->authorize('crear crm');

        if (CrmDeal::where('landing_lead_id', $lead->id)->exists()) {
            return back()->with('error', 'Este prospecto ya fue convertido a una oportunidad del CRM.');
        }

        $contact = $lead->contact ?: Contact::firstOrCreate(
            ['email' => $lead->email],
            ['name' => $lead->name, 'phone' => $lead->phone, 'source' => 'landing', 'consent' => true, 'consent_at' => now(), 'subscribed' => true]
        );

        if (! $lead->contact_id) {
            $lead->update(['contact_id' => $contact->id]);
        }

        $firstOpenStage = CrmStage::where('is_won', false)->where('is_lost', false)->orderBy('sort_order')->first();

        $deal = CrmDeal::create([
            'title' => $lead->landingPage->name.' — '.$contact->name,
            'contact_id' => $contact->id,
            'product_id' => $lead->landingPage->product_id,
            'stage_id' => $firstOpenStage?->id,
            'source' => 'landing',
            'status' => 'abierto',
            'landing_lead_id' => $lead->id,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('crm.edit', $deal)->with('success', 'Prospecto convertido a oportunidad del CRM.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'contact_id' => ['required', 'exists:contacts,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'stage_id' => ['required', 'exists:crm_stages,id'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'expected_close_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);
    }
}
