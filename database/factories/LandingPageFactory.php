<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\LandingPage>
 */
class LandingPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(3, true)),
            'status' => 'borrador',
            'headline' => fake()->sentence(6),
            'subheadline' => fake()->sentence(10),
            'cta_text' => 'Quiero más información',
            'capture_form_enabled' => true,
        ];
    }
}
