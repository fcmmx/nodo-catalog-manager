<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Email\EmailConfig;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailSettingsController extends Controller
{
    public function edit(EmailConfig $config): View
    {
        $this->authorize('configurar campanas');

        return view('admin.email.edit', [
            'enabled' => $config->enabled(),
            'provider' => $config->provider(),
            'host' => $config->host(),
            'port' => $config->port(),
            'username' => $config->username(),
            'encryption' => $config->encryption(),
            'fromName' => $config->fromName(),
            'fromEmail' => $config->fromEmail(),
            'isConfigured' => $config->isConfigured(),
            'hasPassword' => ! empty($config->password()),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('configurar campanas');

        $data = $request->validate([
            'email_marketing_enabled' => ['nullable', 'boolean'],
            'email_marketing_provider' => ['required', 'in:smtp,brevo,mailgun,sendgrid,ses,other'],
            'email_marketing_host' => ['required', 'string', 'max:255'],
            'email_marketing_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'email_marketing_username' => ['required', 'string', 'max:255'],
            'email_marketing_password' => ['nullable', 'string', 'max:500'],
            'email_marketing_encryption' => ['nullable', 'in:tls,ssl,'],
            'email_marketing_from_name' => ['required', 'string', 'max:255'],
            'email_marketing_from_email' => ['required', 'email', 'max:255'],
        ]);

        Setting::set('email_marketing_enabled', $request->boolean('email_marketing_enabled') ? '1' : '0', 'email');
        foreach (['email_marketing_provider', 'email_marketing_host', 'email_marketing_port', 'email_marketing_username', 'email_marketing_encryption', 'email_marketing_from_name', 'email_marketing_from_email'] as $key) {
            Setting::set($key, $data[$key] ?? '', 'email');
        }

        if (! empty($data['email_marketing_password'])) {
            Setting::set('email_marketing_password', $data['email_marketing_password'], 'email', encrypted: true);
        }

        activity('configuracion')->causedBy($request->user())->log('Actualizó la configuración de email marketing');

        return back()->with('success', 'Configuración de email marketing actualizada.');
    }

    public function test(Request $request, EmailConfig $config): JsonResponse
    {
        $this->authorize('configurar campanas');

        $request->validate(['test_email' => ['required', 'email']]);

        if (! $config->isConfigured()) {
            return response()->json(['ok' => false, 'message' => 'Configura y habilita el proveedor antes de probar.'], 422);
        }

        $config->applyRuntimeConfig();

        try {
            Mail::mailer($config->mailerName())
                ->to($request->input('test_email'))
                ->send(new \App\Mail\TestConnectionMailable($config->fromName()));

            return response()->json(['ok' => true, 'message' => 'Correo de prueba enviado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
