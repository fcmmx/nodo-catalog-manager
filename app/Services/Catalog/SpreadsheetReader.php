<?php

namespace App\Services\Catalog;

use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetReader
{
    /**
     * Lee un archivo CSV, XLSX, XLS o JSON y regresa ['headers' => [...], 'rows' => [[...], ...]].
     * La primera fila del archivo (o las llaves del primer objeto JSON) se usan como encabezados.
     */
    public static function read(string $absolutePath, string $extension): array
    {
        $extension = strtolower($extension);

        if ($extension === 'json') {
            return self::readJson($absolutePath);
        }

        if ($extension === 'csv') {
            return self::readCsv($absolutePath);
        }

        return self::readSpreadsheet($absolutePath);
    }

    protected static function readJson(string $path): array
    {
        $decoded = json_decode(file_get_contents($path), true) ?? [];
        $headers = [];
        $rows = [];

        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }
            if (empty($headers)) {
                $headers = array_keys($item);
            }
            $rows[] = array_values($item);
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected static function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $headers = [];
        $rows = [];
        $first = true;

        while (($line = fgetcsv($handle, 0, ',')) !== false) {
            if ($first) {
                $headers = array_map(fn ($h) => trim((string) $h), $line);
                $first = false;

                continue;
            }
            if (count(array_filter($line, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }
            $rows[] = $line;
        }
        fclose($handle);

        return ['headers' => $headers, 'rows' => $rows];
    }

    protected static function readSpreadsheet(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, false);

        $headers = array_map(fn ($h) => trim((string) $h), $data[0] ?? []);
        $rows = [];

        foreach (array_slice($data, 1) as $row) {
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }
            $rows[] = $row;
        }

        return ['headers' => $headers, 'rows' => $rows];
    }
}
