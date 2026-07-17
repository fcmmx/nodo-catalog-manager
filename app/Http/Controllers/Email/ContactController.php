<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactList;
use App\Services\Catalog\SpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('ver contactos');

        $contacts = Contact::query()
            ->with('lists')
            ->when($request->q, fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$request->q}%")->orWhere('email', 'like', "%{$request->q}%")))
            ->when($request->list_id, fn ($q) => $q->whereHas('lists', fn ($l) => $l->where('contact_lists.id', $request->list_id)))
            ->when($request->subscribed === '0', fn ($q) => $q->where('subscribed', false))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('email.contacts.index', ['contacts' => $contacts, 'lists' => ContactList::orderBy('name')->get()]);
    }

    public function create(): View
    {
        $this->authorize('crear contactos');

        return view('email.contacts.form', ['contact' => new Contact, 'lists' => ContactList::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear contactos');

        $data = $this->validated($request);
        $contact = Contact::create($data);
        $contact->lists()->sync($request->input('list_ids', []));

        return redirect()->route('email.contacts.index')->with('success', 'Contacto creado correctamente.');
    }

    public function edit(Contact $contact): View
    {
        $this->authorize('editar contactos');

        return view('email.contacts.form', ['contact' => $contact, 'lists' => ContactList::orderBy('name')->get()]);
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $this->authorize('editar contactos');

        $data = $this->validated($request, $contact->id);
        $contact->update($data);
        $contact->lists()->sync($request->input('list_ids', []));

        return redirect()->route('email.contacts.index')->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->authorize('eliminar contactos');

        $contact->delete();

        return back()->with('success', 'Contacto eliminado.');
    }

    public function importForm(): View
    {
        $this->authorize('importar contactos');

        return view('email.contacts.import', ['lists' => ContactList::orderBy('name')->get()]);
    }

    public function import(Request $request): RedirectResponse
    {
        $this->authorize('importar contactos');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:10240'],
            'list_id' => ['nullable', 'exists:contact_lists,id'],
        ]);

        $file = $request->file('file');
        $storedPath = $file->store('imports', 'local');
        $data = SpreadsheetReader::read(Storage::disk('local')->path($storedPath), $file->getClientOriginalExtension());

        $emailIndex = array_search('email', array_map('strtolower', $data['headers']));
        $nameIndex = array_search('name', array_map('strtolower', $data['headers']));
        if ($nameIndex === false) {
            $nameIndex = array_search('nombre', array_map('strtolower', $data['headers']));
        }
        $phoneIndex = array_search('phone', array_map('strtolower', $data['headers']));
        if ($phoneIndex === false) {
            $phoneIndex = array_search('telefono', array_map('strtolower', $data['headers']));
        }

        if ($emailIndex === false) {
            return back()->with('error', 'El archivo debe tener una columna llamada "email".');
        }

        $imported = 0;
        $skipped = 0;

        foreach ($data['rows'] as $row) {
            $email = trim((string) ($row[$emailIndex] ?? ''));
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;

                continue;
            }

            $contact = Contact::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nameIndex !== false ? ($row[$nameIndex] ?? null) : null,
                    'phone' => $phoneIndex !== false ? ($row[$phoneIndex] ?? null) : null,
                    'source' => 'importacion',
                    'consent' => true,
                    'consent_at' => now(),
                ]
            );

            if ($request->filled('list_id')) {
                $contact->lists()->syncWithoutDetaching([$request->integer('list_id')]);
            }

            $imported++;
        }

        activity('contactos')->causedBy($request->user())->log("Importó {$imported} contacto(s), {$skipped} omitido(s)");

        return redirect()->route('email.contacts.index')->with('success', "Importación completa: {$imported} contacto(s) importado(s), {$skipped} omitido(s) por correo inválido.");
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('exportar contactos');

        $contacts = Contact::query()
            ->when($request->list_id, fn ($q) => $q->whereHas('lists', fn ($l) => $l->where('contact_lists.id', $request->list_id)))
            ->get();

        return response()->streamDownload(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nombre', 'email', 'telefono', 'empresa', 'suscrito', 'consentimiento']);
            foreach ($contacts as $c) {
                fputcsv($handle, [$c->name, $c->email, $c->phone, $c->company, $c->subscribed ? 'si' : 'no', $c->consent ? 'si' : 'no']);
            }
            fclose($handle);
        }, 'contactos-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:contacts,email,'.($ignoreId ?? 'NULL').',id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['consent'] = $request->boolean('consent');
        $data['consent_at'] = $data['consent'] ? now() : null;
        $data['subscribed'] = $request->boolean('subscribed', true);
        $data['source'] = 'manual';

        return $data;
    }
}
