<?php

namespace Tests\Feature\Email;

use App\Mail\CampaignMailable;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\EmailCampaign;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    protected function configureEmailProvider(): void
    {
        Setting::set('email_marketing_enabled', '1', 'email');
        Setting::set('email_marketing_provider', 'smtp', 'email');
        Setting::set('email_marketing_host', 'smtp.example.com', 'email');
        Setting::set('email_marketing_username', 'campaigns@nodo360mkt.site', 'email');
        Setting::set('email_marketing_password', 'secret', 'email', encrypted: true);
        Setting::set('email_marketing_encryption', 'tls', 'email');
        Setting::set('email_marketing_from_name', 'NODO 360 MARKETING TECHNOLOGY', 'email');
        Setting::set('email_marketing_from_email', 'info@nodo360mkt.site', 'email');
    }

    public function test_user_can_create_a_campaign_with_blocks(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/email/campanas', [
            'name' => 'Newsletter de julio',
            'type' => 'newsletter',
            'subject' => 'Novedades de NODO 360',
            'from_name' => 'NODO 360 MARKETING TECHNOLOGY',
            'from_email' => 'info@nodo360mkt.site',
            'blocks' => json_encode([
                ['type' => 'header', 'title' => 'Hola', 'subtitle' => ''],
                ['type' => 'text', 'content' => 'Contenido de prueba'],
            ]),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('email_campaigns', ['name' => 'Newsletter de julio', 'status' => 'borrador']);
        $campaign = EmailCampaign::first();
        $this->assertCount(2, $campaign->blocks);
        $this->assertSame('header', $campaign->blocks[0]['type']);
    }

    public function test_user_without_permission_cannot_create_campaigns(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->post('/email/campanas', [
            'name' => 'Sin permiso', 'type' => 'newsletter', 'subject' => 'x',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site',
        ])->assertForbidden();
    }

    public function test_send_test_fails_clearly_without_provider_configured(): void
    {
        Mail::fake();
        $user = $this->userWithRole('Marketing');
        $campaign = EmailCampaign::create([
            'name' => 'Prueba', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'borrador',
        ]);

        $response = $this->actingAs($user)->post("/email/campanas/{$campaign->id}/prueba", [
            'test_email' => 'destino@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        Mail::assertNothingSent();
    }

    public function test_send_test_succeeds_with_provider_configured(): void
    {
        Mail::fake();
        $this->configureEmailProvider();
        $user = $this->userWithRole('Marketing');
        $campaign = EmailCampaign::create([
            'name' => 'Prueba', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'borrador',
            'blocks' => [['type' => 'text', 'content' => 'Hola']],
        ]);

        $response = $this->actingAs($user)->post("/email/campanas/{$campaign->id}/prueba", [
            'test_email' => 'destino@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        Mail::assertSent(CampaignMailable::class);
        $this->assertDatabaseHas('contacts', ['email' => 'destino@example.com', 'source' => 'prueba']);
    }

    public function test_schedule_requires_a_contact_list(): void
    {
        $user = $this->userWithRole('Marketing');
        $campaign = EmailCampaign::create([
            'name' => 'Sin lista', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'borrador',
        ]);

        $response = $this->actingAs($user)->post("/email/campanas/{$campaign->id}/programar", [
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('email_campaigns', ['id' => $campaign->id, 'status' => 'borrador']);
    }

    public function test_schedule_succeeds_with_a_contact_list(): void
    {
        $user = $this->userWithRole('Marketing');
        $list = ContactList::create(['name' => 'Clientes', 'slug' => 'clientes']);
        $campaign = EmailCampaign::create([
            'name' => 'Con lista', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'borrador',
            'contact_list_id' => $list->id,
        ]);

        $this->actingAs($user)->post("/email/campanas/{$campaign->id}/programar", [
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('email_campaigns', ['id' => $campaign->id, 'status' => 'programada']);
    }

    public function test_command_sends_only_to_subscribed_and_consenting_contacts(): void
    {
        Mail::fake();
        $this->configureEmailProvider();
        $list = ContactList::create(['name' => 'Envío', 'slug' => 'envio']);

        $eligible = Contact::factory()->create(['subscribed' => true, 'consent' => true]);
        $unsubscribed = Contact::factory()->create(['subscribed' => false, 'consent' => true]);
        $noConsent = Contact::factory()->create(['subscribed' => true, 'consent' => false]);
        foreach ([$eligible, $unsubscribed, $noConsent] as $contact) {
            $contact->lists()->attach($list->id);
        }

        $campaign = EmailCampaign::create([
            'name' => 'Envío masivo', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'programada',
            'contact_list_id' => $list->id, 'batch_limit' => 50,
            'blocks' => [['type' => 'text', 'content' => 'Hola a todos']],
        ]);

        $this->artisan('email:send-due-campaigns')->assertExitCode(0);

        $this->assertDatabaseHas('email_campaign_sends', ['email_campaign_id' => $campaign->id, 'contact_id' => $eligible->id]);
        $this->assertDatabaseMissing('email_campaign_sends', ['email_campaign_id' => $campaign->id, 'contact_id' => $unsubscribed->id]);
        $this->assertDatabaseMissing('email_campaign_sends', ['email_campaign_id' => $campaign->id, 'contact_id' => $noConsent->id]);
        Mail::assertSent(CampaignMailable::class, 1);
        $this->assertSame('enviada', $campaign->fresh()->status);
        $this->assertSame(1, $campaign->fresh()->sent_count);
    }

    public function test_pausing_a_campaign_stops_further_sends(): void
    {
        $user = $this->userWithRole('Marketing');
        $campaign = EmailCampaign::create([
            'name' => 'Pausable', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'programada',
        ]);

        $this->actingAs($user)->post("/email/campanas/{$campaign->id}/pausar")->assertRedirect();

        $this->assertDatabaseHas('email_campaigns', ['id' => $campaign->id, 'status' => 'pausada']);
    }

    public function test_report_shows_send_statistics(): void
    {
        $user = $this->userWithRole('Marketing');
        $campaign = EmailCampaign::create([
            'name' => 'Con reporte', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'enviada',
            'sent_count' => 10, 'open_count' => 5,
        ]);

        $response = $this->actingAs($user)->get("/email/campanas/{$campaign->id}/reporte");

        $response->assertOk();
        $response->assertSee('50', false);
    }
}
