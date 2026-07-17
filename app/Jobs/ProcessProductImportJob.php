<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use App\Services\Catalog\ProductImportProcessor;
use App\Services\Catalog\SpreadsheetReader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 600;

    public function __construct(protected int $importBatchId)
    {
    }

    public function handle(): void
    {
        $batch = ImportBatch::findOrFail($this->importBatchId);

        try {
            $absolutePath = Storage::disk('local')->path($batch->stored_path);
            $extension = pathinfo($batch->original_filename, PATHINFO_EXTENSION);

            $data = SpreadsheetReader::read($absolutePath, $extension);

            (new ProductImportProcessor($batch))->process($data['headers'], $data['rows'], $batch->column_mapping ?? []);
        } catch (\Throwable $e) {
            $batch->update([
                'status' => 'fallido',
                'errors' => [['fila' => 0, 'error' => $e->getMessage()]],
                'finished_at' => now(),
            ]);
        }
    }
}
