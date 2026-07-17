<?php

namespace Tests\Feature\Catalog;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_product(): void
    {
        $admin = $this->userWithRole('Superadministrador');

        $response = $this->actingAs($admin)->post('/catalogo/productos', [
            'sku' => 'TEST-001',
            'name' => 'Producto de prueba',
            'type' => 'servicio',
            'currency' => 'MXN',
            'availability' => 'disponible',
            'status' => 'borrador',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['sku' => 'TEST-001', 'name' => 'Producto de prueba']);
    }

    public function test_product_requires_unique_sku(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        Product::factory()->create(['sku' => 'DUP-001']);

        $response = $this->actingAs($admin)->post('/catalogo/productos', [
            'sku' => 'DUP-001',
            'name' => 'Otro producto',
            'type' => 'servicio',
            'currency' => 'MXN',
            'availability' => 'disponible',
            'status' => 'borrador',
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_admin_can_update_a_product(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $product = Product::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($admin)->put("/catalogo/productos/{$product->id}", [
            'sku' => $product->sku,
            'name' => 'Actualizado',
            'type' => 'servicio',
            'currency' => 'MXN',
            'availability' => 'disponible',
            'status' => 'activo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Actualizado', 'status' => 'activo']);
    }

    public function test_deleting_a_product_soft_deletes_it(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $product = Product::factory()->create();

        $this->actingAs($admin)->delete("/catalogo/productos/{$product->id}")->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_a_soft_deleted_product_can_be_restored(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $product = Product::factory()->create();
        $product->delete();

        $this->actingAs($admin)->post("/catalogo/productos/{$product->id}/restaurar")->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_duplicating_a_product_creates_a_draft_copy(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $product = Product::factory()->create(['name' => 'Original', 'status' => 'activo']);

        $this->actingAs($admin)->post("/catalogo/productos/{$product->id}/duplicar")->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'Original (copia)', 'status' => 'borrador']);
    }

    public function test_bulk_update_changes_status_for_multiple_products(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $products = Product::factory()->count(3)->create(['status' => 'borrador']);

        $this->actingAs($admin)->post('/catalogo/productos/masivo', [
            'ids' => $products->pluck('id')->toArray(),
            'action' => 'status',
            'value' => 'activo',
        ])->assertRedirect();

        foreach ($products as $product) {
            $this->assertDatabaseHas('products', ['id' => $product->id, 'status' => 'activo']);
        }
    }

    public function test_products_index_can_be_filtered_by_collection(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        $collection = Collection::factory()->create();
        Product::factory()->create(['collection_id' => $collection->id, 'name' => 'Dentro de la colección']);
        Product::factory()->create(['name' => 'Fuera de la colección']);

        $response = $this->actingAs($admin)->get('/catalogo/productos?collection_id='.$collection->id);

        $response->assertOk();
        $response->assertSee('Dentro de la colección');
        $response->assertDontSee('Fuera de la colección');
    }
}
