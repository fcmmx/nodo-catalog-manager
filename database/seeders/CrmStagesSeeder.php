<?php

namespace Database\Seeders;

use App\Models\CrmStage;
use Illuminate\Database\Seeder;

class CrmStagesSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Nuevo', 'slug' => 'nuevo', 'color' => '#64748B', 'sort_order' => 1],
            ['name' => 'Contactado', 'slug' => 'contactado', 'color' => '#0EA5E9', 'sort_order' => 2],
            ['name' => 'Calificado', 'slug' => 'calificado', 'color' => '#7C3AED', 'sort_order' => 3],
            ['name' => 'Propuesta enviada', 'slug' => 'propuesta-enviada', 'color' => '#F59E0B', 'sort_order' => 4],
            ['name' => 'Negociación', 'slug' => 'negociacion', 'color' => '#DC2626', 'sort_order' => 5],
            ['name' => 'Ganado', 'slug' => 'ganado', 'color' => '#16A34A', 'sort_order' => 6, 'is_won' => true],
            ['name' => 'Perdido', 'slug' => 'perdido', 'color' => '#94A3B8', 'sort_order' => 7, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            CrmStage::firstOrCreate(['slug' => $stage['slug']], $stage);
        }
    }
}
