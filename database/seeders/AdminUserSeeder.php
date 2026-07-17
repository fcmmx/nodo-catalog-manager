<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea el superadministrador inicial. El instalador web reemplaza estos
     * valores por los datos capturados en el asistente; este seeder cubre
     * la instalación por consola (SSH) y el entorno de desarrollo local.
     */
    public function run(): void
    {
        $email = env('NODO_ADMIN_EMAIL', 'admin@nodo360mkt.site');
        $password = env('NODO_ADMIN_PASSWORD', 'Nodo360#Admin2026');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('NODO_ADMIN_NAME', 'Administrador NODO 360'),
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['Superadministrador']);
    }
}
