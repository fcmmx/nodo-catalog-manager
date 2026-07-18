<?php

namespace Tests\Feature\Commerce;

use App\Models\CommerceSyncLog;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Commerce\CommerceFeedConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommerceFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function activeProduct(array $overrides = []): Product
    {
        return Product::factory()->create(array_merge([
            'status' => 'activo',
            'price' => 999.50,
            'url' => 'https://nodo360mkt.site/producto/test',
        ], $overrides));
    }

    public function test_feed_csv_only_includes_eligible_products(): void
    {
        $this->seedRolesAndSettings();
        $eligible = $this->activeProduct(['name' => 'Producto elegible']);
        $draft = $this->activeProduct(['status' => 'borrador', 'name' => 'Producto borrador']);
        $noUrl = $this->activeProduct(['url' => null, 'name' => 'Producto sin url']);
        $noPrice = $this->activeProduct(['price' => null, 'name' => 'Producto sin precio']);

        $token = app(CommerceFeedConfig::class)->feedToken();

        $response = $this->get("/feed/{$token}/catalogo.csv");

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertSee('Producto elegible');
        $response->assertDontSee('Producto borrador');
        $response->assertDontSee('Producto sin url');
        $response->assertDontSee('Producto sin precio');
    }

    public function test_feed_xml_returns_valid_xml_with_eligible_products(): void
    {
        $this->seedRolesAndSettings();
        $this->activeProduct(['name' => 'Producto XML']);
        $token = app(CommerceFeedConfig::class)->feedToken();

        $response = $this->get("/feed/{$token}/catalogo.xml");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertSame('Producto XML', (string) $xml->channel->item->title);
    }

    public function test_feed_rejects_an_invalid_token(): void
    {
        $this->seedRolesAndSettings();
        app(CommerceFeedConfig::class)->feedToken();

        $this->get('/feed/token-incorrecto/catalogo.csv')->assertForbidden();
    }

    public function test_feed_request_is_logged(): void
    {
        $this->seedRolesAndSettings();
        $token = app(CommerceFeedConfig::class)->feedToken();

        $this->get("/feed/{$token}/catalogo.csv");

        $this->assertDatabaseHas('commerce_sync_logs', ['type' => 'feed_csv', 'status' => 'exitoso']);
    }

    public function test_admin_can_update_meta_credentials(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->put('/admin/comercio/configuracion', [
            'meta_catalog_id' => '123456789',
            'meta_business_id' => '987654321',
            'meta_access_token' => 'EAAG-secret-token',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('settings', ['key' => 'meta_catalog_id', 'value' => '123456789']);

        $raw = DB::table('settings')->where('key', 'meta_access_token')->value('value');
        $this->assertNotSame('EAAG-secret-token', $raw);
        $this->assertSame('EAAG-secret-token', Setting::get('meta_access_token'));
    }

    public function test_user_without_permission_cannot_configure_commerce(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->put('/admin/comercio/configuracion', [
            'meta_catalog_id' => '123',
        ])->assertForbidden();
    }

    public function test_connection_test_fails_clearly_without_credentials(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/admin/comercio/configuracion/probar');

        $response->assertStatus(422);
        $response->assertJson(['ok' => false]);
    }

    public function test_connection_test_succeeds_with_valid_credentials(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['name' => 'Catálogo NODO 360', 'product_count' => 12], 200),
        ]);

        $user = $this->userWithRole('Marketing');
        Setting::set('meta_catalog_id', '123456789', 'commerce');
        Setting::set('meta_access_token', 'valid-token', 'commerce', encrypted: true);

        $response = $this->actingAs($user)->post('/admin/comercio/configuracion/probar');

        $response->assertOk();
        $response->assertJson(['ok' => true]);
        $this->assertDatabaseHas('commerce_sync_logs', ['type' => 'connection_test', 'status' => 'exitoso']);
    }

    public function test_connection_test_fails_with_invalid_token(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid OAuth access token']], 401),
        ]);

        $user = $this->userWithRole('Marketing');
        Setting::set('meta_catalog_id', '123456789', 'commerce');
        Setting::set('meta_access_token', 'invalid-token', 'commerce', encrypted: true);

        $response = $this->actingAs($user)->post('/admin/comercio/configuracion/probar');

        $response->assertStatus(422);
        $response->assertJson(['ok' => false]);
        $this->assertDatabaseHas('commerce_sync_logs', ['type' => 'connection_test', 'status' => 'error']);
    }

    public function test_regenerating_the_feed_token_changes_the_url(): void
    {
        $user = $this->userWithRole('Marketing');
        $config = app(CommerceFeedConfig::class);
        $originalToken = $config->feedToken();

        $this->actingAs($user)->post('/admin/comercio/configuracion/regenerar-token')->assertRedirect();

        $this->assertNotSame($originalToken, $config->feedToken());
    }

    public function test_old_feed_token_stops_working_after_regeneration(): void
    {
        $this->seedRolesAndSettings();
        $user = $this->userWithRole('Marketing');
        $config = app(CommerceFeedConfig::class);
        $originalToken = $config->feedToken();

        $this->actingAs($user)->post('/admin/comercio/configuracion/regenerar-token');

        $this->get("/feed/{$originalToken}/catalogo.csv")->assertForbidden();
    }

    public function test_history_page_lists_sync_logs(): void
    {
        $user = $this->userWithRole('Marketing');
        CommerceSyncLog::create(['type' => 'feed_csv', 'status' => 'exitoso', 'products_count' => 5]);

        $response = $this->actingAs($user)->get('/admin/comercio/historial');

        $response->assertOk();
        $response->assertSee('Feed CSV');
    }
}
