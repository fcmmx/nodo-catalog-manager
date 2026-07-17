<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessProductImportJob;
use App\Models\ImportBatch;
use App\Services\Catalog\ProductImportProcessor;
use App\Services\Catalog\SpreadsheetReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function index(): View
    {
        $batches = ImportBatch::with('user')->latest()->paginate(15);

        return view('catalog.import.index', ['batches' => $batches, 'fields' => ProductImportProcessor::FIELDS]);
    }

    public function template(): StreamedResponse
    {
        $headers = array_keys(ProductImportProcessor::FIELDS);

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);
            fputcsv($handle, [
                'IA-100', 'Agente IA de Ejemplo', 'Descripción corta de ejemplo', 'Descripción completa de ejemplo',
                'Inteligencia Artificial', 'Inteligencia Artificial', 'servicio', '4990', '', 'MXN',
                'disponible', 'borrador', 'ia, ejemplo', 'ejemplo', 'Hola, quiero información sobre este servicio.',
                'https://nodo360mkt.site', '',
            ]);
            fclose($handle);
        }, 'plantilla-importacion-productos.csv', ['Content-Type' => 'text/csv']);
    }

    public function upload(Request $request): View|RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls,json', 'max:10240'],
            'duplicate_strategy' => ['required', 'in:skip,update'],
        ]);

        $file = $request->file('file');
        $storedPath = $file->store('imports', 'local');
        $extension = $file->getClientOriginalExtension();

        $data = SpreadsheetReader::read(Storage::disk('local')->path($storedPath), $extension);

        if (empty($data['headers'])) {
            return back()->with('error', 'No se pudo leer el archivo. Verifica que tenga una fila de encabezados.');
        }

        $batch = ImportBatch::create([
            'user_id' => $request->user()->id,
            'type' => 'products',
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'status' => 'mapeo_pendiente',
            'total_rows' => count($data['rows']),
            'duplicate_strategy' => $request->duplicate_strategy,
        ]);

        return view('catalog.import.map', [
            'batch' => $batch,
            'headers' => $data['headers'],
            'previewRows' => array_slice($data['rows'], 0, 5),
            'fields' => ProductImportProcessor::FIELDS,
        ]);
    }

    public function mapAndProcess(Request $request, ImportBatch $importBatch): RedirectResponse
    {
        $mapping = $request->input('mapping', []);
        $mapping = array_filter($mapping, fn ($v) => $v !== null && $v !== '');

        if (! isset($mapping['sku']) || ! isset($mapping['name'])) {
            return back()->with('error', 'Debes mapear al menos las columnas SKU y Nombre.');
        }

        $importBatch->update(['column_mapping' => $mapping]);

        ProcessProductImportJob::dispatch($importBatch->id);

        return redirect()->route('catalog.import.show', $importBatch)
            ->with('success', 'Importación en cola. El progreso se actualizará conforme se procesen los registros.');
    }

    public function show(ImportBatch $importBatch): View
    {
        return view('catalog.import.show', ['batch' => $importBatch]);
    }

    public function downloadErrors(ImportBatch $importBatch): StreamedResponse
    {
        if (! $importBatch->errors_file_path || ! Storage::disk('local')->exists($importBatch->errors_file_path)) {
            abort(404, 'No hay un reporte de errores disponible para esta importación.');
        }

        return Storage::disk('local')->download($importBatch->errors_file_path, 'errores-importacion-'.$importBatch->id.'.csv');
    }
}
