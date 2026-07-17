<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Collection;
use App\Models\ImportBatch;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportProcessor
{
    public const FIELDS = [
        'sku' => 'SKU (obligatorio)',
        'name' => 'Nombre (obligatorio)',
        'short_description' => 'Descripción corta',
        'description' => 'Descripción completa',
        'category' => 'Categoría',
        'collection' => 'Colección',
        'type' => 'Tipo (producto/servicio)',
        'price' => 'Precio',
        'old_price' => 'Precio anterior',
        'currency' => 'Moneda',
        'availability' => 'Disponibilidad',
        'status' => 'Estado',
        'keywords' => 'Palabras clave',
        'tags' => 'Etiquetas (separadas por coma)',
        'whatsapp_message' => 'Mensaje de WhatsApp',
        'url' => 'URL',
        'main_image' => 'Imagen principal (URL)',
    ];

    protected const CHUNK = 50;

    public function __construct(protected ImportBatch $batch)
    {
    }

    public function process(array $headers, array $rows, array $mapping): void
    {
        $this->batch->update([
            'status' => 'processing',
            'total_rows' => count($rows),
            'started_at' => now(),
        ]);

        $errors = [];
        $success = 0;
        $processed = 0;

        foreach (array_chunk($rows, self::CHUNK, true) as $chunk) {
            foreach ($chunk as $index => $row) {
                $rowNumber = $index + 2; // +1 por encabezado, +1 base 1
                $processed++;

                try {
                    $values = $this->mapRow($row, $mapping);
                    $result = $this->importRow($values, $rowNumber);

                    if ($result === true) {
                        $success++;
                    } else {
                        $errors[] = ['fila' => $rowNumber, 'error' => $result];
                    }
                } catch (\Throwable $e) {
                    $errors[] = ['fila' => $rowNumber, 'error' => $e->getMessage()];
                }
            }

            $this->batch->update([
                'processed_rows' => $processed,
                'success_rows' => $success,
                'error_rows' => count($errors),
            ]);
        }

        $errorsPath = null;
        if (! empty($errors)) {
            $errorsPath = 'imports/errors/batch-'.$this->batch->id.'-'.time().'.csv';
            $handle = fopen('php://temp', 'w+');
            fputcsv($handle, ['fila', 'error']);
            foreach ($errors as $e) {
                fputcsv($handle, [$e['fila'], $e['error']]);
            }
            rewind($handle);
            Storage::disk('local')->put($errorsPath, stream_get_contents($handle));
            fclose($handle);
        }

        $this->batch->update([
            'status' => empty($errors) ? 'completado' : ($success > 0 ? 'completado_con_errores' : 'fallido'),
            'errors' => array_slice($errors, 0, 200),
            'errors_file_path' => $errorsPath,
            'finished_at' => now(),
        ]);
    }

    protected function mapRow(array $row, array $mapping): array
    {
        $values = [];
        foreach ($mapping as $field => $columnIndex) {
            if ($columnIndex === null || $columnIndex === '') {
                continue;
            }
            $values[$field] = isset($row[$columnIndex]) ? trim((string) $row[$columnIndex]) : null;
        }

        return $values;
    }

    protected function importRow(array $values, int $rowNumber): true|string
    {
        if (empty($values['sku'])) {
            return 'El SKU es obligatorio.';
        }
        if (empty($values['name'])) {
            return 'El nombre es obligatorio.';
        }

        $existing = Product::withTrashed()->where('sku', $values['sku'])->first();

        if ($existing && $this->batch->duplicate_strategy === 'skip') {
            return 'SKU duplicado, se omitió según la estrategia seleccionada.';
        }

        $categoryId = null;
        if (! empty($values['category'])) {
            $categoryId = Category::firstOrCreate(
                ['slug' => Str::slug($values['category'])],
                ['name' => $values['category'], 'is_active' => true]
            )->id;
        }

        $collectionId = null;
        if (! empty($values['collection'])) {
            $collectionId = Collection::firstOrCreate(
                ['slug' => Str::slug($values['collection'])],
                ['name' => $values['collection'], 'is_active' => true]
            )->id;
        }

        $type = in_array($values['type'] ?? null, Product::TYPES) ? $values['type'] : 'servicio';
        $availability = in_array($values['availability'] ?? null, Product::AVAILABILITIES) ? $values['availability'] : 'disponible';
        $status = in_array($values['status'] ?? null, Product::STATUSES) ? $values['status'] : 'borrador';

        $payload = [
            'name' => $values['name'],
            'category_id' => $categoryId,
            'collection_id' => $collectionId,
            'type' => $type,
            'short_description' => $values['short_description'] ?? null,
            'description' => $values['description'] ?? null,
            'price' => is_numeric($values['price'] ?? null) ? $values['price'] : null,
            'old_price' => is_numeric($values['old_price'] ?? null) ? $values['old_price'] : null,
            'currency' => $values['currency'] ?? 'MXN',
            'availability' => $availability,
            'status' => $status,
            'keywords' => $values['keywords'] ?? null,
            'tags' => ! empty($values['tags']) ? array_map('trim', explode(',', $values['tags'])) : [],
            'whatsapp_message' => $values['whatsapp_message'] ?? null,
            'url' => $values['url'] ?? null,
            'main_image' => $values['main_image'] ?? null,
        ];

        if ($existing) {
            $existing->update($payload);
        } else {
            Product::create(array_merge($payload, [
                'sku' => $values['sku'],
                'slug' => Product::uniqueSlug(Str::slug($values['name'])),
            ]));
        }

        return true;
    }
}
