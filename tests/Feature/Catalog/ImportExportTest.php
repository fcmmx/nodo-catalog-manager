<?php

namespace Tests\Feature\Catalog;

use App\Models\ImportBatch;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_rejects_unsupported_file_types(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole('Superadministrador');
        $file = UploadedFile::fake()->create('malware.exe', 10);

        $response = $this->actingAs($admin)->post('/catalogo/importar-exportar/subir', [
            'file' => $file,
            'duplicate_strategy' => 'skip',
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_csv_file_can_be_uploaded_and_mapped(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole('Superadministrador');

        $csv = "sku,name,price\nIMP-001,Producto Importado,1999\n";
        $file = UploadedFile::fake()->createWithContent('productos.csv', $csv);

        $response = $this->actingAs($admin)->post('/catalogo/importar-exportar/subir', [
            'file' => $file,
            'duplicate_strategy' => 'skip',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('import_batches', ['original_filename' => 'productos.csv', 'total_rows' => 1]);
    }

    public function test_mapping_and_processing_a_batch_creates_products(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole('Superadministrador');

        $csv = "sku,name,price\nIMP-002,Producto Importado Dos,2999\n";
        $path = 'imports/test-batch.csv';
        Storage::disk('local')->put($path, $csv);

        $batch = ImportBatch::create([
            'user_id' => $admin->id,
            'type' => 'products',
            'original_filename' => 'test-batch.csv',
            'stored_path' => $path,
            'status' => 'mapeo_pendiente',
            'total_rows' => 1,
            'duplicate_strategy' => 'skip',
        ]);

        $response = $this->actingAs($admin)->post("/catalogo/importar-exportar/{$batch->id}/mapear", [
            'mapping' => ['sku' => '0', 'name' => '1', 'price' => '2'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['sku' => 'IMP-002', 'name' => 'Producto Importado Dos']);
        $this->assertDatabaseHas('import_batches', ['id' => $batch->id, 'success_rows' => 1, 'error_rows' => 0]);
    }

    public function test_duplicate_sku_is_skipped_when_strategy_is_skip(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole('Superadministrador');
        Product::factory()->create(['sku' => 'DUP-100', 'name' => 'Ya existe']);

        $path = 'imports/dup-batch.csv';
        Storage::disk('local')->put($path, "sku,name\nDUP-100,Nuevo nombre\n");

        $batch = ImportBatch::create([
            'user_id' => $admin->id, 'type' => 'products', 'original_filename' => 'dup-batch.csv',
            'stored_path' => $path, 'status' => 'mapeo_pendiente', 'total_rows' => 1, 'duplicate_strategy' => 'skip',
        ]);

        $this->actingAs($admin)->post("/catalogo/importar-exportar/{$batch->id}/mapear", [
            'mapping' => ['sku' => '0', 'name' => '1'],
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'DUP-100', 'name' => 'Ya existe']);
        $this->assertDatabaseHas('import_batches', ['id' => $batch->id, 'error_rows' => 1]);
    }

    public function test_export_returns_csv_with_product_data(): void
    {
        $admin = $this->userWithRole('Superadministrador');
        Product::factory()->create(['sku' => 'EXP-001', 'name' => 'Producto Exportable']);

        $response = $this->actingAs($admin)->get('/catalogo/productos/exportar?format=csv');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('EXP-001', $response->streamedContent());
    }
}
