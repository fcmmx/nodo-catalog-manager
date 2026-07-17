<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/catalogo/productos')->assertRedirect('/login');
        $this->get('/admin/usuarios')->assertRedirect('/login');
        $this->get('/admin/configuracion')->assertRedirect('/login');
    }

    public function test_user_without_permission_cannot_access_users_admin(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->get('/admin/usuarios')->assertForbidden();
    }

    public function test_user_without_permission_cannot_create_products(): void
    {
        $user = $this->userWithRole('Solo lectura');

        $this->actingAs($user)->get('/catalogo/productos/create')->assertForbidden();
        $this->actingAs($user)->post('/catalogo/productos', ['name' => 'X'])->assertForbidden();
    }

    public function test_readonly_user_can_view_catalog(): void
    {
        $user = $this->userWithRole('Solo lectura');

        $this->actingAs($user)->get('/catalogo/productos')->assertOk();
    }
}
