<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Jobs\SendCampaignEmailJob;
use App\Mail\CampaignMailable;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignSend;
use App\Models\Product;
use App\Services\Email\EmailConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver campanas');

        $campaigns = EmailCampaign::query()
            ->with('list')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('email.campaigns.index', ['campaigns' => $campaigns, 'statuses' => EmailCampaign::STATUSES]);
    }

    public function create(): View
    {
        $this->authorize('crear campanas');

        return view('email.campaigns.form', [
            'campaign' => new EmailCampaign,
            'lists' => ContactList::withCount('contacts')->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear campanas');

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'borrador';

        $campaign = EmailCampaign::create($data);

        return redirect()->route('email.campaigns.edit', $campaign)->with('success', 'Campaña creada correctamente.');
    }

    public function edit(EmailCampaign $campaign): View
    {
        $this->authorize('editar campanas');

        return view('email.campaigns.form', [
            'campaign' => $campaign,
            'lists' => ContactList::withCount('contacts')->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('editar campanas');

        if (! in_array($campaign->status, ['borrador', 'programada', 'pausada'])) {
            return back()->with('error', 'Esta campaña ya se envió y no se puede editar.');
        }

        $data = $this->validated($request);
        $campaign->update($data);

        return redirect()->route('email.campaigns.edit', $campaign)->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('eliminar campanas');

        $campaign->delete();

        return redirect()->route('email.campaigns.index')->with('success', 'Campaña eliminada.');
    }

    public function sendTest(Request $request, EmailCampaign $campaign, EmailConfig $config): RedirectResponse
    {
        $this->authorize('enviar campanas');

        $request->validate(['test_email' => ['required', 'email']]);

        if (! $config->isConfigured()) {
            return back()->with('error', 'Configura un proveedor de email marketing antes de enviar pruebas (Email Marketing → Configuración).');
        }

        $testContact = Contact::firstOrCreate(
            ['email' => $request->input('test_email')],
            ['name' => 'Prueba', 'consent' => true, 'consent_at' => now(), 'subscribed' => true, 'source' => 'prueba']
        );

        $send = EmailCampaignSend::create([
            'email_campaign_id' => $campaign->id,
            'contact_id' => $testContact->id,
            'token' => Str::random(48),
            'status' => 'pendiente',
        ]);

        $config->applyRuntimeConfig();

        try {
            Mail::mailer($config->mailerName())->to($testContact->email)->send(new CampaignMailable($send));
            $send->update(['status' => 'enviado', 'sent_at' => now()]);

            return back()->with('success', "Correo de prueba enviado a {$testContact->email}.");
        } catch (\Throwable $e) {
            $send->update(['status' => 'error', 'error_message' => $e->getMessage()]);

            return back()->with('error', 'No se pudo enviar el correo de prueba: '.$e->getMessage());
        }
    }

    public function schedule(Request $request, EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('enviar campanas');

        $data = $request->validate(['scheduled_at' => ['required', 'date']]);

        if (! $campaign->contact_list_id) {
            return back()->with('error', 'Selecciona una lista de contactos antes de programar la campaña.');
        }

        $campaign->update(['status' => 'programada', 'scheduled_at' => $data['scheduled_at']]);

        activity('campanas')->causedBy($request->user())->log('Programó la campaña "'.$campaign->name.'"');

        return back()->with('success', 'Campaña programada correctamente.');
    }

    public function sendNow(EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('enviar campanas');

        if (! $campaign->contact_list_id) {
            return back()->with('error', 'Selecciona una lista de contactos antes de enviar la campaña.');
        }

        $campaign->update(['status' => 'programada', 'scheduled_at' => now()]);

        return back()->with('success', 'Campaña marcada para envío inmediato. Se procesará en los próximos minutos según el cron configurado.');
    }

    public function pause(EmailCampaign $campaign): RedirectResponse
    {
        $this->authorize('enviar campanas');

        $campaign->update(['status' => 'pausada']);

        return back()->with('success', 'Campaña pausada. Los envíos pendientes no se enviarán hasta reanudarla.');
    }

    public function report(EmailCampaign $campaign): View
    {
        $this->authorize('ver campanas');

        $sends = $campaign->sends()->with('contact')->latest()->paginate(30);

        return view('email.campaigns.report', ['campaign' => $campaign, 'sends' => $sends]);
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', array_keys(EmailCampaign::TYPES))],
            'subject' => ['required', 'string', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
            'from_email' => ['required', 'email', 'max:255'],
            'contact_list_id' => ['nullable', 'exists:contact_lists,id'],
            'batch_limit' => ['nullable', 'integer', 'min:5', 'max:500'],
            'blocks' => ['nullable', 'string'],
        ]);

        $data['blocks'] = $data['blocks'] ? json_decode($data['blocks'], true) : [];
        $data['batch_limit'] = $data['batch_limit'] ?? 50;

        return $data;
    }
}
