<?php

namespace Tests\Feature\Ai;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function configureAi(): void
    {
        Setting::set('ai_enabled', '1', 'ai');
        Setting::set('ai_provider', 'openai', 'ai');
        Setting::set('ai_model', 'gpt-4o-mini', 'ai');
        Setting::set('ai_base_url', 'https://api.openai.com/v1', 'ai');
        Setting::set('ai_api_key', 'sk-test-key-1234', 'ai', encrypted: true);
    }

    public function test_generation_fails_clearly_when_ai_is_not_configured(): void
    {
        $user = $this->userWithRole('Superadministrador');

        $response = $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'reason' => 'not_configured']);
        $this->assertDatabaseCount('ai_generations', 0);
    }

    public function test_generation_succeeds_with_a_configured_provider(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $this->configureAi();

        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Atiende a tus clientes por WhatsApp 24/7 con IA.']]],
                'usage' => ['prompt_tokens' => 42, 'completion_tokens' => 18],
            ], 200),
        ]);

        $response = $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ]);

        $response->assertOk();
        $response->assertJson(['ok' => true, 'content' => 'Atiende a tus clientes por WhatsApp 24/7 con IA.']);

        $this->assertDatabaseHas('ai_generations', [
            'task' => 'descripcion_corta',
            'status' => 'completado',
            'input_tokens' => 42,
            'output_tokens' => 18,
        ]);
    }

    public function test_generation_reports_invalid_token_error(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $this->configureAi();

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => ['message' => 'Incorrect API key provided']], 401),
        ]);

        $response = $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'reason' => 'invalid_token']);
        $this->assertDatabaseHas('ai_generations', ['status' => 'error']);
    }

    public function test_generation_reports_rate_limit_error(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $this->configureAi();

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => ['message' => 'Too many requests']], 429),
        ]);

        $response = $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['ok' => false, 'reason' => 'rate_limited']);
    }

    public function test_generation_reports_quota_exceeded_error(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $this->configureAi();

        Http::fake([
            'api.openai.com/*' => Http::response(['error' => ['message' => 'You exceeded your current quota']], 429),
        ]);

        $response = $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ]);

        $response->assertJson(['ok' => false, 'reason' => 'quota_exceeded']);
    }

    public function test_user_without_permission_cannot_generate_content(): void
    {
        $user = $this->userWithRole('Cliente');
        $this->configureAi();

        $this->actingAs($user)->postJson('/ia/generar', [
            'task' => 'descripcion_corta',
            'tema' => 'Agente IA para WhatsApp',
        ])->assertForbidden();
    }

    public function test_generator_page_shows_warning_when_not_configured(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->get('/ia/generador');

        $response->assertOk();
        $response->assertSee('no está configurado');
    }

    public function test_approve_and_reject_update_generation_status(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $this->configureAi();

        Http::fake(['api.openai.com/*' => Http::response([
            'choices' => [['message' => ['content' => 'Texto generado']]],
        ], 200)]);

        $this->actingAs($user)->postJson('/ia/generar', ['task' => 'descripcion_corta', 'tema' => 'X']);
        $generation = \App\Models\AiGeneration::first();

        $this->actingAs($user)->post("/ia/generaciones/{$generation->id}/aprobar")->assertRedirect();
        $this->assertDatabaseHas('ai_generations', ['id' => $generation->id, 'status' => 'aprobado']);

        $this->actingAs($user)->post("/ia/generaciones/{$generation->id}/rechazar")->assertRedirect();
        $this->assertDatabaseHas('ai_generations', ['id' => $generation->id, 'status' => 'rechazado']);
    }
}
