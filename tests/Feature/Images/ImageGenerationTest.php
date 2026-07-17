<?php

namespace Tests\Feature\Images;

use App\Models\ImageTemplate;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function masterTemplate(): ImageTemplate
    {
        return ImageTemplate::create([
            'name' => 'Plantilla maestra NODO 360',
            'slug' => 'plantilla-maestra-nodo-360',
            'format' => 'cuadrado',
            'width' => 1080,
            'height' => 1080,
            'background_type' => 'color',
            'background_value' => '#F8FAFC',
            'overlay_gradient' => true,
            'primary_color' => '#2563EB',
            'accent_color' => '#DC2626',
            'title_position' => 'center',
            'show_price' => true,
            'show_qr' => false,
            'footer_text' => 'NODO 360 Marketing Technology',
            'is_master' => true,
        ]);
    }

    public function test_generator_page_loads_for_authorized_user(): void
    {
        $user = $this->userWithRole('Diseñador');
        $this->masterTemplate();

        $this->actingAs($user)->get('/imagenes/generador')->assertOk();
    }

    public function test_user_can_generate_an_image_with_a_gradient_background(): void
    {
        Storage::fake('public');
        $user = $this->userWithRole('Diseñador');
        $template = $this->masterTemplate();

        $response = $this->actingAs($user)->post('/imagenes/generar', [
            'template_id' => $template->id,
            'title' => 'Agente IA para WhatsApp',
            'subtitle' => 'Atiende a tus clientes 24/7',
            'cta_text' => 'Agenda una demostración',
            'price_text' => 'Desde $4,990 MXN',
            'background_source' => 'color',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('image_generations', [
            'template_id' => $template->id,
            'status' => 'completado',
        ]);

        $generation = \App\Models\ImageGeneration::first();
        Storage::disk('public')->assertExists($generation->file_path);
    }

    public function test_generated_image_can_be_attached_to_a_product(): void
    {
        Storage::fake('public');
        $user = $this->userWithRole('Superadministrador');
        $template = $this->masterTemplate();
        $product = Product::factory()->create();

        $this->actingAs($user)->post('/imagenes/generar', [
            'template_id' => $template->id,
            'product_id' => $product->id,
            'title' => $product->name,
            'background_source' => 'color',
        ]);

        $generation = \App\Models\ImageGeneration::first();

        $this->actingAs($user)->post("/imagenes/generaciones/{$generation->id}/usar-principal")->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'main_image' => $generation->file_path]);

        $this->actingAs($user)->post("/imagenes/generaciones/{$generation->id}/galeria")->assertRedirect();
        $this->assertDatabaseHas('product_images', ['product_id' => $product->id, 'path' => $generation->file_path]);
    }

    public function test_user_without_permission_cannot_generate_images(): void
    {
        $user = $this->userWithRole('Cliente');
        $template = $this->masterTemplate();

        $this->actingAs($user)->post('/imagenes/generar', [
            'template_id' => $template->id,
            'background_source' => 'color',
        ])->assertForbidden();
    }

    public function test_master_template_cannot_be_deleted(): void
    {
        $user = $this->userWithRole('Superadministrador');
        $template = $this->masterTemplate();

        $response = $this->actingAs($user)->delete("/imagenes/plantillas/{$template->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('image_templates', ['id' => $template->id, 'deleted_at' => null]);
    }

    public function test_non_master_template_can_be_created_and_deleted(): void
    {
        $user = $this->userWithRole('Superadministrador');

        $this->actingAs($user)->post('/imagenes/plantillas', [
            'name' => 'Plantilla de prueba',
            'format' => 'cuadrado',
            'background_type' => 'color',
            'background_value' => '#000000',
            'primary_color' => '#2563EB',
            'accent_color' => '#DC2626',
            'title_position' => 'center',
        ])->assertRedirect();

        $template = ImageTemplate::where('name', 'Plantilla de prueba')->firstOrFail();

        $this->actingAs($user)->delete("/imagenes/plantillas/{$template->id}")->assertRedirect();
        $this->assertSoftDeleted('image_templates', ['id' => $template->id]);
    }
}
