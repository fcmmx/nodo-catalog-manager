<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'general' => Setting::group('general'),
            'security' => Setting::group('security'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('administrar configuracion');

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'system_name' => ['required', 'string', 'max:255'],
            'system_subtitle' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_whatsapp' => ['nullable', 'string', 'max:30'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'currency' => ['required', 'string', 'max:3'],
            'timezone' => ['required', 'string', 'max:64'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'hero_text' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:1024'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico', 'max:256'],
            'login_max_attempts' => ['required', 'integer', 'min:3', 'max:20'],
            'login_lockout_minutes' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            $data['favicon_path'] = $request->file('favicon')->store('branding', 'public');
        }

        foreach (['company_name', 'system_name', 'system_subtitle', 'company_address', 'company_phone', 'company_whatsapp', 'company_email', 'company_website', 'currency', 'timezone', 'tax_rate', 'cta_text', 'hero_text', 'primary_color', 'accent_color', 'logo_path', 'favicon_path'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], 'general');
            }
        }

        foreach (['login_max_attempts', 'login_lockout_minutes'] as $key) {
            Setting::set($key, $data[$key], 'security');
        }

        activity('configuracion')->causedBy($request->user())->log('Actualizó la configuración del sistema');

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
