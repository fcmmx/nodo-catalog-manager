<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CrmStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CrmDeal>
 */
class CrmDealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucfirst(fake()->unique()->words(3, true)),
            'contact_id' => Contact::factory(),
            'stage_id' => fn () => CrmStage::first()?->id ?? CrmStage::factory(),
            'currency' => 'MXN',
            'source' => 'manual',
            'status' => 'abierto',
        ];
    }
}
