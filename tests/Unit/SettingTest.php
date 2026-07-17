<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_set_and_get_roundtrip(): void
    {
        Setting::set('company_name', 'NODO 360 MARKETING TECHNOLOGY');

        $this->assertSame('NODO 360 MARKETING TECHNOLOGY', Setting::get('company_name'));
    }

    public function test_encrypted_settings_are_stored_encrypted_and_decrypted_on_read(): void
    {
        Setting::set('ai_api_key', 'sk-super-secret-value', 'ai', encrypted: true);

        $raw = DB::table('settings')->where('key', 'ai_api_key')->value('value');

        $this->assertNotSame('sk-super-secret-value', $raw);
        $this->assertSame('sk-super-secret-value', Setting::get('ai_api_key'));
    }

    public function test_get_returns_default_when_missing(): void
    {
        $this->assertSame('fallback', Setting::get('clave_inexistente', 'fallback'));
    }
}
