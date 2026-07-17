<?php

namespace App\Services\Install;

class RequirementsChecker
{
    public static function phpVersion(): array
    {
        return [
            'label' => 'PHP versión 8.2 o superior',
            'detail' => 'Versión actual: '.PHP_VERSION,
            'ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
        ];
    }

    public static function extensions(): array
    {
        $required = ['pdo_mysql', 'mbstring', 'openssl', 'gd', 'zip', 'fileinfo', 'tokenizer', 'ctype', 'json', 'curl', 'bcmath'];

        return array_map(fn ($ext) => [
            'label' => "Extensión PHP: {$ext}",
            'detail' => extension_loaded($ext) ? 'Instalada' : 'No encontrada',
            'ok' => extension_loaded($ext),
        ], $required);
    }

    public static function permissions(): array
    {
        $paths = [
            'storage/' => storage_path(),
            'storage/framework/' => storage_path('framework'),
            'storage/framework/cache/' => storage_path('framework/cache'),
            'storage/framework/sessions/' => storage_path('framework/sessions'),
            'storage/framework/views/' => storage_path('framework/views'),
            'storage/logs/' => storage_path('logs'),
            'storage/app/' => storage_path('app'),
            'bootstrap/cache/' => base_path('bootstrap/cache'),
            '.env' => base_path('.env'),
        ];

        return array_map(fn ($label, $path) => [
            'label' => "Permisos de escritura: {$label}",
            'detail' => is_writable($path) ? 'Escribible' : 'Sin permisos de escritura',
            'ok' => is_writable($path),
        ], array_keys($paths), $paths);
    }

    public static function allPassed(): bool
    {
        $checks = array_merge([self::phpVersion()], self::extensions(), self::permissions());

        return collect($checks)->every(fn ($c) => $c['ok']);
    }
}
