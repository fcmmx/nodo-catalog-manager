<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_general_settings(): void
    {
        $admin = $this->userWithRole('Superadministrador');

        $response = $this->actingAs($admin)->put('/admin/configuracion', [
            'company_name' => 'NODO 360 MARKETING TECHNOLOGY',
            'system_name' => 'NODO Catalog Manager',
            'currency' => 'MXN',
            'timezone' => 'America/Mexico_City',
            'tax_rate' => 16,
            'login_max_attempts' => 5,
            'login_lockout_minutes' => 15,
        ]);

        $response->assertRedirect();
        $this->assertSame('NODO 360 MARKETING TECHNOLOGY', Setting::get('company_name'));
        $this->assertSame('America/Mexico_City', Setting::get('timezone'));
    }

    public function test_user_without_permission_cannot_update_settings(): void
    {
        $user = $this->userWithRole('Ventas');

        $this->actingAs($user)->put('/admin/configuracion', [
            'company_name' => 'Intento no autorizado',
            'system_name' => 'X',
            'currency' => 'MXN',
            'timezone' => 'America/Mexico_City',
            'tax_rate' => 16,
            'login_max_attempts' => 5,
            'login_lockout_minutes' => 15,
        ])->assertForbidden();
    }
}
