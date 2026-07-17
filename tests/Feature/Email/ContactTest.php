<?php

namespace Tests\Feature\Email;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignSend;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_contact_with_consent(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/email/contactos', [
            'name' => 'Ana Pérez',
            'email' => 'ana@example.com',
            'consent' => '1',
            'subscribed' => '1',
        ]);

        $response->assertRedirect(route('email.contacts.index'));
        $this->assertDatabaseHas('contacts', [
            'email' => 'ana@example.com', 'source' => 'manual', 'consent' => 1, 'subscribed' => 1,
        ]);
    }

    public function test_contact_can_be_attached_to_lists(): void
    {
        $user = $this->userWithRole('Marketing');
        $list = ContactList::create(['name' => 'Newsletter', 'slug' => 'newsletter']);

        $this->actingAs($user)->post('/email/contactos', [
            'email' => 'lista@example.com',
            'consent' => '1',
            'list_ids' => [$list->id],
        ]);

        $contact = Contact::where('email', 'lista@example.com')->first();
        $this->assertTrue($contact->lists->contains($list));
    }

    public function test_user_without_permission_cannot_create_contacts(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->post('/email/contactos', [
            'email' => 'sinpermiso@example.com',
        ])->assertForbidden();
    }

    public function test_contacts_can_be_imported_from_csv(): void
    {
        Storage::fake('local');
        $user = $this->userWithRole('Marketing');

        $csv = "name,email,phone\nCarlos Ruiz,carlos@example.com,5551234567\n,correo-invalido,000\n";
        $file = UploadedFile::fake()->createWithContent('contactos.csv', $csv);

        $response = $this->actingAs($user)->post('/email/contactos/importar', ['file' => $file]);

        $response->assertRedirect(route('email.contacts.index'));
        $this->assertDatabaseHas('contacts', ['email' => 'carlos@example.com', 'source' => 'importacion', 'consent' => 1]);
        $this->assertDatabaseCount('contacts', 1);
    }

    public function test_export_returns_csv_with_contact_data(): void
    {
        $user = $this->userWithRole('Marketing');
        Contact::factory()->create(['email' => 'exportable@example.com', 'name' => 'Exportable']);

        $response = $this->actingAs($user)->get('/email/contactos/exportar');

        $response->assertOk();
        $this->assertStringContainsString('exportable@example.com', $response->streamedContent());
    }

    public function test_public_unsubscribe_page_requires_a_valid_token(): void
    {
        $this->get('/email/baja/token-inexistente')->assertNotFound();
    }

    public function test_public_unsubscribe_marks_contact_and_campaign(): void
    {
        $contact = Contact::factory()->create(['subscribed' => true]);
        $campaign = EmailCampaign::create([
            'name' => 'Campaña', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'enviada',
        ]);
        $send = EmailCampaignSend::create([
            'email_campaign_id' => $campaign->id, 'contact_id' => $contact->id,
            'token' => str_repeat('a', 48), 'status' => 'enviado',
        ]);

        $this->get('/email/baja/'.$send->token)->assertOk();
        $response = $this->post('/email/baja/'.$send->token);

        $response->assertOk();
        $this->assertFalse($contact->fresh()->subscribed);
        $this->assertNotNull($contact->fresh()->unsubscribed_at);
        $this->assertSame(1, $campaign->fresh()->unsubscribe_count);
    }

    public function test_tracking_pixel_marks_send_as_opened_once(): void
    {
        $contact = Contact::factory()->create();
        $campaign = EmailCampaign::create([
            'name' => 'Campaña', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'enviada',
        ]);
        $send = EmailCampaignSend::create([
            'email_campaign_id' => $campaign->id, 'contact_id' => $contact->id,
            'token' => str_repeat('b', 48), 'status' => 'enviado',
        ]);

        $this->get('/email/abrir/'.$send->token)->assertOk();
        $this->get('/email/abrir/'.$send->token)->assertOk();

        $this->assertNotNull($send->fresh()->opened_at);
        $this->assertSame(1, $campaign->fresh()->open_count);
    }

    public function test_tracking_click_redirects_and_marks_send(): void
    {
        $contact = Contact::factory()->create();
        $campaign = EmailCampaign::create([
            'name' => 'Campaña', 'type' => 'newsletter', 'subject' => 'Asunto',
            'from_name' => 'NODO', 'from_email' => 'info@nodo360mkt.site', 'status' => 'enviada',
        ]);
        $send = EmailCampaignSend::create([
            'email_campaign_id' => $campaign->id, 'contact_id' => $contact->id,
            'token' => str_repeat('c', 48), 'status' => 'enviado',
        ]);
        $target = base64_encode('https://nodo360mkt.site/producto/1');

        $response = $this->get('/email/clic/'.$send->token.'?url='.$target);

        $response->assertRedirect('https://nodo360mkt.site/producto/1');
        $this->assertNotNull($send->fresh()->clicked_at);
        $this->assertSame(1, $campaign->fresh()->click_count);
    }
}
