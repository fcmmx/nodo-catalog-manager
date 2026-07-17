<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialAccountController extends Controller
{
    public function index(): View
    {
        $this->authorize('ver redes');

        return view('social.accounts.index', ['accounts' => SocialAccount::withCount('posts')->orderBy('channel')->get()]);
    }

    public function create(): View
    {
        $this->authorize('conectar cuentas redes');

        return view('social.accounts.form', ['account' => new SocialAccount]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('conectar cuentas redes');

        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        SocialAccount::create($data);

        activity('redes')->causedBy($request->user())->log('Conectó la cuenta de '.$data['channel'].': '.$data['label']);

        return redirect()->route('social.accounts.index')->with('success', 'Cuenta guardada correctamente.');
    }

    public function edit(SocialAccount $account): View
    {
        $this->authorize('conectar cuentas redes');

        return view('social.accounts.form', ['account' => $account]);
    }

    public function update(Request $request, SocialAccount $account): RedirectResponse
    {
        $this->authorize('conectar cuentas redes');

        $data = $this->validated($request, requireToken: false);

        if (empty($data['access_token'])) {
            unset($data['access_token']);
        }

        $account->update($data);

        return redirect()->route('social.accounts.index')->with('success', 'Cuenta actualizada correctamente.');
    }

    public function destroy(SocialAccount $account): RedirectResponse
    {
        $this->authorize('conectar cuentas redes');

        $account->delete();

        return back()->with('success', 'Cuenta desconectada.');
    }

    protected function validated(Request $request, bool $requireToken = true): array
    {
        $data = $request->validate([
            'channel' => ['required', 'in:'.implode(',', SocialAccount::CHANNELS)],
            'label' => ['required', 'string', 'max:255'],
            'external_account_id' => ['nullable', 'string', 'max:255'],
            'access_token' => [$requireToken ? 'nullable' : 'nullable', 'string', 'max:2000'],
            'token_expires_at' => ['nullable', 'date'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
