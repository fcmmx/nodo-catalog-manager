<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\CrmStagesSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedRolesAndSettings(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(SettingsSeeder::class);
        $this->seed(CrmStagesSeeder::class);
    }

    protected function userWithRole(string $role, array $attributes = []): User
    {
        $this->seedRolesAndSettings();

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
