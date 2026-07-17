<?php

namespace Tests\Feature\Ai;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_configure_ai_provider(): void
    {
        $admin = $this->userWithRole('Superadministrador');

        $response = $this->actingAs($admin)->put('/admin/ia/configuracion', [
            'ai_enabled' => '1',
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4o-mini',
            'ai_base_url' => 'https://api.openai.com/v1',
            'ai_api_key' => 'sk-real-key-example',
        ]);

        $response->assertRedirect();
        $this->assertSame('1', Setting::get('ai_enabled'));
        $this->assertSame('sk-real-key-example', Setting::get('ai_api_key'));
    }

    public function test_api_key_is_never_stored_in_plain_text(): void
    {
        $admin = $this->userWithRole('Superadministrador');

        $this->actingAs($admin)->put('/admin/ia/configuracion', [
            'ai_enabled' => '1',
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4o-mini',
            'ai_base_url' => 'https://api.openai.com/v1',
            'ai_api_key' => 'sk-real-key-example',
        ]);

        $raw = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'ai_api_key')->value('value');

        $this->assertNotSame('sk-real-key-example', $raw);
    }

    public function test_leaving_api_key_blank_keeps_the_previous_value(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        Setting::set('ai_api_key', 'sk-original-key', 'ai', encrypted: true);

        $this->actingAs($admin)->put('/admin/ia/configuracion', [
            'ai_enabled' => '1',
            'ai_provider' => 'openai',
            'ai_model' => 'gpt-4o-mini',
            'ai_base_url' => 'https://api.openai.com/v1',
            'ai_api_key' => '',
        ]);

        $this->assertSame('sk-original-key', Setting::get('ai_api_key'));
    }

    public function test_user_without_permission_cannot_view_ai_settings(): void
    {
        $user = $this->userWithRole('Editor');

        $this->actingAs($user)->get('/admin/ia/configuracion')->assertForbidden();
    }
}
