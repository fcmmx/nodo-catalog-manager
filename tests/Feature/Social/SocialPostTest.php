<?php

namespace Tests\Feature\Social;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SocialPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_draft_post(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/redes/publicaciones', [
            'channel' => 'facebook',
            'content' => 'Conoce nuestro Agente IA para WhatsApp',
            'hashtags' => '#NODO360',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('social_posts', ['channel' => 'facebook', 'status' => 'borrador']);
    }

    public function test_post_with_scheduled_date_is_marked_as_scheduled(): void
    {
        $user = $this->userWithRole('Marketing');

        $this->actingAs($user)->post('/redes/publicaciones', [
            'channel' => 'facebook',
            'content' => 'Publicación programada',
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ]);

        $this->assertDatabaseHas('social_posts', ['content' => 'Publicación programada', 'status' => 'programada']);
    }

    public function test_publishing_without_an_authorized_account_fails_clearly(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $post = SocialPost::create([
            'user_id' => $user->id, 'channel' => 'facebook', 'content' => 'Prueba', 'status' => 'programada',
        ]);

        $response = $this->actingAs($user)->post("/redes/publicaciones/{$post->id}/publicar");

        $response->assertRedirect();
        $this->assertDatabaseHas('social_posts', ['id' => $post->id, 'status' => 'error']);
    }

    public function test_publishing_to_facebook_succeeds_with_an_authorized_account_and_mocked_api(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['id' => '123456_987654'], 200)]);

        $user = $this->userWithRole('Superadministrador');
        $account = SocialAccount::create([
            'channel' => 'facebook', 'label' => 'Página de prueba', 'external_account_id' => '123456',
            'access_token' => 'test-page-token', 'is_active' => true,
        ]);
        $post = SocialPost::create([
            'user_id' => $user->id, 'social_account_id' => $account->id, 'channel' => 'facebook',
            'content' => 'Prueba de publicación', 'status' => 'programada',
        ]);

        $response = $this->actingAs($user)->post("/redes/publicaciones/{$post->id}/publicar");

        $response->assertRedirect();
        $this->assertDatabaseHas('social_posts', [
            'id' => $post->id, 'status' => 'enviada', 'external_post_id' => '123456_987654',
        ]);
    }

    public function test_publishing_to_an_unsupported_channel_is_marked_pending_authorization(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $account = SocialAccount::create([
            'channel' => 'linkedin', 'label' => 'LinkedIn de prueba', 'access_token' => 'x', 'is_active' => true,
        ]);
        $post = SocialPost::create([
            'user_id' => $user->id, 'social_account_id' => $account->id, 'channel' => 'linkedin',
            'content' => 'Prueba', 'status' => 'programada',
        ]);

        $this->actingAs($user)->post("/redes/publicaciones/{$post->id}/publicar");

        $this->assertDatabaseHas('social_posts', ['id' => $post->id, 'status' => 'pendiente_autorizacion']);
    }

    public function test_command_publishes_all_due_scheduled_posts(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['id' => 'abc123'], 200)]);

        $user = $this->userWithRole('Superadministrador');
        $account = SocialAccount::create([
            'channel' => 'facebook', 'label' => 'Página', 'external_account_id' => '1',
            'access_token' => 'token', 'is_active' => true,
        ]);
        $due = SocialPost::create([
            'user_id' => $user->id, 'social_account_id' => $account->id, 'channel' => 'facebook',
            'content' => 'Vencida', 'status' => 'programada', 'scheduled_at' => now()->subMinute(),
        ]);
        $future = SocialPost::create([
            'user_id' => $user->id, 'social_account_id' => $account->id, 'channel' => 'facebook',
            'content' => 'Futura', 'status' => 'programada', 'scheduled_at' => now()->addDay(),
        ]);

        $this->artisan('social:publish-due')->assertExitCode(0);

        $this->assertDatabaseHas('social_posts', ['id' => $due->id, 'status' => 'enviada']);
        $this->assertDatabaseHas('social_posts', ['id' => $future->id, 'status' => 'programada']);
    }

    public function test_user_can_duplicate_a_post_for_another_channel(): void
    {
        $user = $this->userWithRole('Marketing');
        $post = SocialPost::create([
            'user_id' => $user->id, 'channel' => 'facebook', 'content' => 'Original', 'status' => 'borrador',
        ]);

        $response = $this->actingAs($user)->post("/redes/publicaciones/{$post->id}/duplicar", ['channel' => 'instagram']);

        $response->assertRedirect();
        $this->assertDatabaseHas('social_posts', ['channel' => 'instagram', 'content' => 'Original', 'duplicated_from' => $post->id]);
    }

    public function test_user_can_mark_a_post_as_published_manually(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $post = SocialPost::create([
            'user_id' => $user->id, 'channel' => 'tiktok', 'content' => 'Prueba', 'status' => 'programada',
        ]);

        $this->actingAs($user)->post("/redes/publicaciones/{$post->id}/publicar-manual")->assertRedirect();

        $this->assertDatabaseHas('social_posts', ['id' => $post->id, 'status' => 'publicada_manual']);
    }

    public function test_user_without_permission_cannot_create_posts(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->post('/redes/publicaciones', [
            'channel' => 'facebook',
            'content' => 'No autorizado',
        ])->assertForbidden();
    }

    public function test_account_token_is_stored_encrypted(): void
    {
        $user = $this->userWithRole('Superadministrador');

        $this->actingAs($user)->post('/redes/cuentas', [
            'channel' => 'facebook',
            'label' => 'Cuenta de prueba',
            'access_token' => 'super-secret-token',
        ]);

        $raw = \Illuminate\Support\Facades\DB::table('social_accounts')->value('access_token');

        $this->assertNotSame('super-secret-token', $raw);
        $this->assertSame('super-secret-token', SocialAccount::first()->decrypted_access_token);
    }

    public function test_calendar_export_downloads_csv(): void
    {
        Storage::fake('public');
        $user = $this->userWithRole('Marketing');
        SocialPost::create([
            'user_id' => $user->id, 'channel' => 'facebook', 'content' => 'Exportable',
            'status' => 'programada', 'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->get('/redes/calendario/exportar');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
