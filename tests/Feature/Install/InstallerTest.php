<?php

namespace Tests\Feature\Install;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_step_shows_requirement_checks(): void
    {
        $response = $this->get('/install');

        $response->assertOk();
        $response->assertSee('PHP versión 8.2 o superior');
        $response->assertSee('Permisos de escritura');
    }

    public function test_company_step_redirects_back_when_database_step_is_missing(): void
    {
        $this->get('/install/empresa')->assertRedirect('/install/base-datos');
    }

    public function test_admin_step_redirects_back_when_company_step_is_missing(): void
    {
        $this->get('/install/administrador')->assertRedirect('/install/empresa');
    }

    public function test_database_form_rejects_invalid_connection(): void
    {
        $response = $this->post('/install/base-datos', [
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'una_base_que_no_existe_xyz',
            'username' => 'usuario_invalido',
            'password' => 'clave_invalida',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_run_step_redirects_to_welcome_when_wizard_data_is_incomplete(): void
    {
        $response = $this->get('/install/instalar');

        $response->assertRedirect('/install');
    }
}
