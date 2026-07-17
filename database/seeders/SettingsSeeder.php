<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $general = [
            'company_name' => 'NODO 360 MARKETING TECHNOLOGY',
            'system_name' => 'NODO Catalog Manager',
            'system_subtitle' => 'El centro inteligente de contenidos, catálogos y automatización de NODO 360.',
            'company_address' => '',
            'company_phone' => '',
            'company_whatsapp' => '',
            'company_email' => 'info@nodo360mkt.site',
            'company_website' => 'https://nodo360mkt.site',
            'currency' => 'MXN',
            'timezone' => 'America/Mexico_City',
            'locale' => 'es',
            'tax_rate' => '16',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
            'primary_color' => '#0F172A',
            'accent_color' => '#DC2626',
            'gradient_from' => '#2563EB',
            'gradient_to' => '#7C3AED',
            'logo_path' => '',
            'favicon_path' => '',
            'cta_text' => 'Agenda una demostración',
            'hero_text' => 'Centraliza, crea, automatiza y publica todo el contenido comercial de tu empresa desde un solo lugar.',
        ];

        foreach ($general as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        $security = [
            'login_max_attempts' => '5',
            'login_lockout_minutes' => '15',
            'require_email_verification' => '0',
        ];

        foreach ($security as $key => $value) {
            Setting::set($key, $value, 'security');
        }

        Setting::set('installed', '1', 'system');
    }
}
