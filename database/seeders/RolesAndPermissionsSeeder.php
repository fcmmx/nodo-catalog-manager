<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Permisos independientes por módulo, según sección 16 del brief.
     */
    protected array $permissions = [
        'productos' => ['ver', 'crear', 'editar', 'eliminar', 'publicar', 'exportar', 'importar'],
        'colecciones' => ['ver', 'crear', 'editar', 'eliminar'],
        'categorias' => ['ver', 'crear', 'editar', 'eliminar'],
        'usuarios' => ['ver', 'crear', 'editar', 'eliminar', 'administrar'],
        'configuracion' => ['ver', 'administrar', 'configurar integraciones'],
        'actividad' => ['ver'],
        'reportes' => ['ver'],
        'informacion sensible' => ['acceder'],
        'ia' => ['usar', 'ver historial', 'configurar'],
        'imagenes' => ['ver', 'crear', 'editar', 'eliminar'],
        'redes' => ['ver', 'crear', 'editar', 'eliminar', 'aprobar', 'publicar', 'conectar cuentas'],
        'contactos' => ['ver', 'crear', 'editar', 'eliminar', 'importar', 'exportar'],
        'campanas' => ['ver', 'crear', 'editar', 'eliminar', 'enviar', 'configurar'],
        'landing' => ['ver', 'crear', 'editar', 'eliminar', 'publicar'],
        'crm' => ['ver', 'crear', 'editar', 'eliminar', 'asignar'],
        'comercio' => ['ver', 'configurar'],
        'auditoria' => ['ver', 'crear'],
    ];

    public function run(): void
    {
        Cache::forget('spatie.permission.cache');

        $allPermissions = [];
        foreach ($this->permissions as $module => $actions) {
            foreach ($actions as $action) {
                $name = "{$action} {$module}";
                Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $allPermissions[] = $name;
            }
        }

        $roles = [
            'Superadministrador' => $allPermissions,
            'Administrador' => array_filter($allPermissions, fn ($p) => ! str_contains($p, 'informacion sensible')),
            'Marketing' => [
                'ver productos', 'crear productos', 'editar productos', 'publicar productos', 'exportar productos',
                'ver colecciones', 'ver categorias', 'ver actividad', 'ver reportes', 'usar ia', 'ver historial ia',
                'ver imagenes', 'crear imagenes', 'editar imagenes',
                'ver redes', 'crear redes', 'editar redes', 'aprobar redes', 'publicar redes', 'conectar cuentas redes',
                'ver contactos', 'crear contactos', 'editar contactos', 'importar contactos', 'exportar contactos',
                'ver campanas', 'crear campanas', 'editar campanas', 'enviar campanas', 'configurar campanas',
                'ver landing', 'crear landing', 'editar landing', 'publicar landing',
                'ver crm', 'crear crm', 'editar crm', 'asignar crm',
                'ver comercio', 'configurar comercio',
                'ver auditoria', 'crear auditoria',
            ],
            'Diseñador' => [
                'ver productos', 'editar productos', 'ver colecciones', 'ver categorias', 'usar ia',
                'ver imagenes', 'crear imagenes', 'editar imagenes', 'eliminar imagenes',
                'ver redes', 'crear redes', 'editar redes',
                'ver campanas', 'crear campanas', 'editar campanas',
                'ver landing', 'crear landing', 'editar landing',
            ],
            'Ventas' => [
                'ver productos', 'exportar productos', 'ver colecciones', 'ver categorias', 'ver reportes',
                'ver contactos', 'crear contactos', 'editar contactos',
                'ver landing',
                'ver crm', 'crear crm', 'editar crm',
            ],
            'Editor' => [
                'ver productos', 'crear productos', 'editar productos', 'ver colecciones', 'ver categorias', 'usar ia',
                'ver imagenes', 'crear imagenes',
                'ver redes', 'crear redes', 'editar redes',
                'ver campanas', 'crear campanas', 'editar campanas',
                'ver landing', 'crear landing', 'editar landing',
            ],
            'Analista' => [
                'ver productos', 'ver colecciones', 'ver categorias', 'ver actividad', 'ver reportes', 'ver historial ia', 'ver redes',
                'ver contactos', 'ver campanas', 'ver landing', 'ver crm', 'ver comercio', 'ver auditoria',
            ],
            'Cliente' => [
                'ver productos', 'ver colecciones', 'ver categorias',
            ],
            'Solo lectura' => [
                'ver productos', 'ver colecciones', 'ver categorias', 'ver usuarios', 'ver configuracion', 'ver actividad', 'ver reportes', 'ver historial ia',
                'ver contactos', 'ver campanas', 'ver landing', 'ver crm', 'ver comercio', 'ver auditoria',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
