<?php

namespace Tests\Feature\Landing;

use App\Models\Contact;
use App\Models\ContactList;
use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_landing_page_with_sections(): void
    {
        $user = $this->userWithRole('Marketing');

        $response = $this->actingAs($user)->post('/landing', [
            'name' => 'Landing de prueba',
            'headline' => 'Título principal',
            'subheadline' => 'Subtítulo',
            'cta_text' => 'Quiero más información',
            'sections' => json_encode([
                ['type' => 'texto', 'title' => 'Sección', 'content' => 'Contenido de prueba'],
            ]),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('landing_pages', ['name' => 'Landing de prueba', 'status' => 'borrador']);
        $landing = LandingPage::first();
        $this->assertNotEmpty($landing->slug);
        $this->assertCount(1, $landing->sections);
    }

    public function test_user_without_permission_cannot_create_landing_pages(): void
    {
        $user = $this->userWithRole('Cliente');

        $this->actingAs($user)->post('/landing', [
            'name' => 'Sin permiso', 'headline' => 'x', 'cta_text' => 'y',
        ])->assertForbidden();
    }

    public function test_publishing_requires_headline_and_sections(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create(['sections' => null]);

        $response = $this->actingAs($user)->post("/landing/{$landing->id}/publicar");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('landing_pages', ['id' => $landing->id, 'status' => 'borrador']);
    }

    public function test_publishing_succeeds_with_headline_and_sections(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create([
            'sections' => [['type' => 'texto', 'content' => 'Hola']],
        ]);

        $this->actingAs($user)->post("/landing/{$landing->id}/publicar")->assertRedirect();

        $this->assertDatabaseHas('landing_pages', ['id' => $landing->id, 'status' => 'publicada']);
        $this->assertNotNull($landing->fresh()->published_at);
    }

    public function test_draft_landing_pages_are_not_publicly_accessible(): void
    {
        $landing = LandingPage::factory()->create(['status' => 'borrador']);

        $this->get('/lp/'.$landing->slug)->assertNotFound();
    }

    public function test_published_landing_page_renders_publicly_and_counts_a_view(): void
    {
        $product = Product::factory()->create();
        $landing = LandingPage::factory()->create([
            'status' => 'publicada',
            'product_id' => $product->id,
            'sections' => [
                ['type' => 'faq', 'title' => 'FAQ', 'items' => [['heading' => '¿Pregunta?', 'text' => 'Respuesta']]],
            ],
        ]);

        $response = $this->get('/lp/'.$landing->slug);

        $response->assertOk();
        $response->assertSee($landing->headline);
        $response->assertSee('FAQPage', false);
        $this->assertSame(1, $landing->fresh()->views_count);
    }

    public function test_lead_capture_creates_lead_and_increments_count(): void
    {
        $landing = LandingPage::factory()->create(['status' => 'publicada', 'capture_form_enabled' => true]);

        $response = $this->post('/lp/'.$landing->slug.'/prospecto', [
            'name' => 'Juan Pérez', 'email' => 'juan@example.com', 'phone' => '5551234567',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('landing_leads', ['landing_page_id' => $landing->id, 'email' => 'juan@example.com']);
        $this->assertSame(1, $landing->fresh()->leads_count);
    }

    public function test_lead_capture_creates_a_contact_when_a_list_is_configured(): void
    {
        $list = ContactList::create(['name' => 'Prospectos', 'slug' => 'prospectos']);
        $landing = LandingPage::factory()->create([
            'status' => 'publicada', 'capture_form_enabled' => true, 'contact_list_id' => $list->id,
        ]);

        $this->post('/lp/'.$landing->slug.'/prospecto', [
            'name' => 'Ana López', 'email' => 'ana@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('contacts', ['email' => 'ana@example.com', 'source' => 'landing']);
        $contact = Contact::where('email', 'ana@example.com')->first();
        $this->assertTrue($contact->lists->contains($list));
        $lead = \App\Models\LandingLead::where('email', 'ana@example.com')->first();
        $this->assertSame($contact->id, $lead->contact_id);
    }

    public function test_lead_capture_without_list_does_not_create_a_contact(): void
    {
        $landing = LandingPage::factory()->create(['status' => 'publicada', 'capture_form_enabled' => true, 'contact_list_id' => null]);

        $this->post('/lp/'.$landing->slug.'/prospecto', [
            'name' => 'Sin lista', 'email' => 'sinlista@example.com',
        ])->assertRedirect();

        $this->assertDatabaseMissing('contacts', ['email' => 'sinlista@example.com']);
        $this->assertDatabaseHas('landing_leads', ['email' => 'sinlista@example.com', 'contact_id' => null]);
    }

    public function test_lead_capture_is_blocked_when_form_disabled(): void
    {
        $landing = LandingPage::factory()->create(['status' => 'publicada', 'capture_form_enabled' => false]);

        $this->post('/lp/'.$landing->slug.'/prospecto', [
            'name' => 'X', 'email' => 'x@example.com',
        ])->assertNotFound();
    }

    public function test_user_can_duplicate_a_landing_page(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create(['status' => 'publicada', 'views_count' => 10, 'leads_count' => 2]);

        $response = $this->actingAs($user)->post("/landing/{$landing->id}/duplicar");

        $response->assertRedirect();
        $this->assertDatabaseHas('landing_pages', ['name' => $landing->name.' (copia)', 'status' => 'borrador', 'views_count' => 0, 'leads_count' => 0]);
    }

    public function test_unpublishing_reverts_status_to_draft(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create(['status' => 'publicada']);

        $this->actingAs($user)->post("/landing/{$landing->id}/despublicar")->assertRedirect();

        $this->assertDatabaseHas('landing_pages', ['id' => $landing->id, 'status' => 'borrador']);
    }

    public function test_qr_code_download_returns_a_png_image(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create(['status' => 'publicada']);

        $response = $this->actingAs($user)->get("/landing/{$landing->id}/qr");

        $response->assertOk();
        $this->assertSame('image/png', $response->headers->get('content-type'));
    }

    public function test_leads_report_shows_captured_leads(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create();
        $landing->leads()->create(['name' => 'Lead Uno', 'email' => 'lead1@example.com']);

        $response = $this->actingAs($user)->get("/landing/{$landing->id}/prospectos");

        $response->assertOk();
        $response->assertSee('lead1@example.com');
    }
}
