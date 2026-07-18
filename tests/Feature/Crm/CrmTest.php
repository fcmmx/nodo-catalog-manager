<?php

namespace Tests\Feature\Crm;

use App\Models\Contact;
use App\Models\CrmActivity;
use App\Models\CrmDeal;
use App\Models\CrmStage;
use App\Models\LandingLead;
use App\Models\LandingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_deal(): void
    {
        $user = $this->userWithRole('Marketing');
        $contact = Contact::factory()->create();
        $stage = CrmStage::where('slug', 'nuevo')->first();

        $response = $this->actingAs($user)->post('/crm', [
            'title' => 'Oportunidad de prueba',
            'contact_id' => $contact->id,
            'stage_id' => $stage->id,
            'value' => 1500,
            'currency' => 'MXN',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('crm_deals', ['title' => 'Oportunidad de prueba', 'status' => 'abierto']);
    }

    public function test_user_without_permission_cannot_create_deals(): void
    {
        $user = $this->userWithRole('Cliente');
        $contact = Contact::factory()->create();
        $stage = CrmStage::where('slug', 'nuevo')->first();

        $this->actingAs($user)->post('/crm', [
            'title' => 'Sin permiso', 'contact_id' => $contact->id, 'stage_id' => $stage->id,
        ])->assertForbidden();
    }

    public function test_moving_a_deal_to_a_won_stage_updates_status(): void
    {
        $user = $this->userWithRole('Marketing');
        $deal = CrmDeal::factory()->create(['stage_id' => CrmStage::where('slug', 'nuevo')->first()->id]);
        $wonStage = CrmStage::where('is_won', true)->first();

        $response = $this->actingAs($user)->post("/crm/{$deal->id}/mover", ['stage_id' => $wonStage->id]);

        $response->assertOk();
        $this->assertDatabaseHas('crm_deals', ['id' => $deal->id, 'stage_id' => $wonStage->id, 'status' => 'ganado']);
    }

    public function test_moving_a_deal_to_a_lost_stage_updates_status(): void
    {
        $user = $this->userWithRole('Marketing');
        $deal = CrmDeal::factory()->create(['stage_id' => CrmStage::where('slug', 'nuevo')->first()->id]);
        $lostStage = CrmStage::where('is_lost', true)->first();

        $this->actingAs($user)->post("/crm/{$deal->id}/mover", ['stage_id' => $lostStage->id])->assertOk();

        $this->assertDatabaseHas('crm_deals', ['id' => $deal->id, 'status' => 'perdido']);
    }

    public function test_mark_won_moves_deal_to_won_stage(): void
    {
        $user = $this->userWithRole('Marketing');
        $deal = CrmDeal::factory()->create(['stage_id' => CrmStage::where('slug', 'nuevo')->first()->id]);

        $this->actingAs($user)->post("/crm/{$deal->id}/ganado")->assertRedirect();

        $deal->refresh();
        $this->assertSame('ganado', $deal->status);
        $this->assertTrue($deal->stage->is_won);
    }

    public function test_mark_lost_records_a_reason(): void
    {
        $user = $this->userWithRole('Marketing');
        $deal = CrmDeal::factory()->create(['stage_id' => CrmStage::where('slug', 'nuevo')->first()->id]);

        $this->actingAs($user)->post("/crm/{$deal->id}/perdido", ['lost_reason' => 'Eligió a la competencia'])->assertRedirect();

        $this->assertDatabaseHas('crm_deals', ['id' => $deal->id, 'status' => 'perdido', 'lost_reason' => 'Eligió a la competencia']);
    }

    public function test_activities_can_be_added_and_completed(): void
    {
        $user = $this->userWithRole('Marketing');
        $deal = CrmDeal::factory()->create();

        $this->actingAs($user)->post("/crm/{$deal->id}/actividades", [
            'type' => 'tarea',
            'content' => 'Llamar mañana',
            'due_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ])->assertRedirect();

        $activity = CrmActivity::first();
        $this->assertNotNull($activity);
        $this->assertTrue($activity->isReminder());
        $this->assertFalse($activity->isOverdue());

        $this->actingAs($user)->post("/crm/actividades/{$activity->id}/completar")->assertRedirect();
        $this->assertNotNull($activity->fresh()->completed_at);
    }

    public function test_overdue_reminder_is_detected(): void
    {
        $deal = CrmDeal::factory()->create();
        $activity = CrmActivity::create([
            'deal_id' => $deal->id, 'type' => 'tarea', 'due_at' => now()->subDay(),
        ]);

        $this->assertTrue($activity->isOverdue());
    }

    public function test_converting_a_landing_lead_creates_a_deal_and_contact(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create();
        $lead = LandingLead::create([
            'landing_page_id' => $landing->id, 'name' => 'Prospecto Landing', 'email' => 'lead@example.com', 'phone' => '5551234567',
        ]);

        $response = $this->actingAs($user)->post("/crm/convertir/{$lead->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', ['email' => 'lead@example.com', 'source' => 'landing']);
        $this->assertDatabaseHas('crm_deals', ['landing_lead_id' => $lead->id, 'source' => 'landing']);
    }

    public function test_converting_the_same_lead_twice_fails_clearly(): void
    {
        $user = $this->userWithRole('Marketing');
        $landing = LandingPage::factory()->create();
        $lead = LandingLead::create([
            'landing_page_id' => $landing->id, 'name' => 'Prospecto Landing', 'email' => 'dup@example.com',
        ]);

        $this->actingAs($user)->post("/crm/convertir/{$lead->id}");
        $response = $this->actingAs($user)->post("/crm/convertir/{$lead->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame(1, CrmDeal::where('landing_lead_id', $lead->id)->count());
    }

    public function test_whatsapp_url_uses_contact_whatsapp_number(): void
    {
        $contact = Contact::factory()->create(['whatsapp' => '+52 55 1234 5678', 'phone' => null]);
        $deal = CrmDeal::factory()->create(['contact_id' => $contact->id]);

        $this->assertSame('https://wa.me/525512345678', $deal->whatsappUrl());
    }

    public function test_whatsapp_url_is_null_without_a_phone(): void
    {
        $contact = Contact::factory()->create(['whatsapp' => null, 'phone' => null]);
        $deal = CrmDeal::factory()->create(['contact_id' => $contact->id]);

        $this->assertNull($deal->whatsappUrl());
    }

    public function test_stage_cannot_be_deleted_when_it_has_deals(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $stage = CrmStage::where('slug', 'nuevo')->first();
        CrmDeal::factory()->create(['stage_id' => $stage->id]);

        $response = $this->actingAs($user)->delete("/crm/etapas/{$stage->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('crm_stages', ['id' => $stage->id]);
    }

    public function test_assigning_a_deal_to_a_user(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $deal = CrmDeal::factory()->create();

        $this->actingAs($user)->post("/crm/{$deal->id}/asignar", ['assigned_to' => $user->id])->assertRedirect();

        $this->assertDatabaseHas('crm_deals', ['id' => $deal->id, 'assigned_to' => $user->id]);
    }

    public function test_pipeline_board_only_shows_open_deals(): void
    {
        $user = $this->userWithRole('Marketing');
        $wonStage = CrmStage::where('is_won', true)->first();
        $openStage = CrmStage::where('slug', 'nuevo')->first();

        $openDeal = CrmDeal::factory()->create(['stage_id' => $openStage->id, 'status' => 'abierto', 'title' => 'Abierta visible']);
        CrmDeal::factory()->create(['stage_id' => $wonStage->id, 'status' => 'ganado', 'title' => 'Ganada oculta']);

        $response = $this->actingAs($user)->get('/crm');

        $response->assertOk();
        $response->assertSee('Abierta visible');
        $response->assertDontSee('Ganada oculta');
    }
}
