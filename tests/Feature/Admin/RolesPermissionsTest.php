<?php

namespace Tests\Feature\Admin;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_the_nine_initial_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $expected = [
            'Superadministrador', 'Administrador', 'Marketing', 'Diseñador', 'Ventas',
            'Editor', 'Analista', 'Cliente', 'Solo lectura',
        ];

        foreach ($expected as $role) {
            $this->assertTrue(Role::where('name', $role)->exists(), "Falta el rol {$role}");
        }
    }

    public function test_superadmin_has_all_permissions_and_solo_lectura_has_only_view_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $superadmin = Role::where('name', 'Superadministrador')->first();
        $soloLectura = Role::where('name', 'Solo lectura')->first();

        $this->assertGreaterThan(20, $superadmin->permissions()->count());

        foreach ($soloLectura->permissions as $permission) {
            $this->assertStringStartsWith('ver ', $permission->name);
        }
    }

    public function test_admin_can_assign_roles_to_a_user(): void
    {
        $admin = $this->userWithRole('Superadministrador');

        $response = $this->actingAs($admin)->post('/admin/usuarios', [
            'name' => 'Nuevo Editor',
            'email' => 'editor@nodo360mkt.site',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'roles' => ['Editor'],
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'editor@nodo360mkt.site']);
    }
}
