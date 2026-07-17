<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactListController extends Controller
{
    public function index(): View
    {
        $this->authorize('ver contactos');

        return view('email.lists.index', ['lists' => ContactList::withCount('contacts')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        $this->authorize('crear contactos');

        return view('email.lists.form', ['list' => new ContactList]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('crear contactos');

        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:500']]);
        $data['slug'] = Str::slug($data['name']);

        ContactList::create($data);

        return redirect()->route('email.lists.index')->with('success', 'Lista creada correctamente.');
    }

    public function edit(ContactList $list): View
    {
        $this->authorize('editar contactos');

        return view('email.lists.form', ['list' => $list]);
    }

    public function update(Request $request, ContactList $list): RedirectResponse
    {
        $this->authorize('editar contactos');

        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:500']]);
        $data['slug'] = Str::slug($data['name']);

        $list->update($data);

        return redirect()->route('email.lists.index')->with('success', 'Lista actualizada correctamente.');
    }

    public function destroy(ContactList $list): RedirectResponse
    {
        $this->authorize('eliminar contactos');

        $list->delete();

        return back()->with('success', 'Lista eliminada.');
    }
}
