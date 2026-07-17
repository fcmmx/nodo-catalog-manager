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
                'ver colecciones', 'ver categorias', 'ver actividad', 'ver reportes',
            ],
            'Diseñador' => [
                'ver productos', 'editar productos', 'ver colecciones', 'ver categorias',
            ],
            'Ventas' => [
                'ver productos', 'exportar productos', 'ver colecciones', 'ver categorias', 'ver reportes',
            ],
            'Editor' => [
                'ver productos', 'crear productos', 'editar productos', 'ver colecciones', 'ver categorias',
            ],
            'Analista' => [
                'ver productos', 'ver colecciones', 'ver categorias', 'ver actividad', 'ver reportes',
            ],
            'Cliente' => [
                'ver productos', 'ver colecciones', 'ver categorias',
            ],
            'Solo lectura' => [
                'ver productos', 'ver colecciones', 'ver categorias', 'ver usuarios', 'ver configuracion', 'ver actividad', 'ver reportes',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
