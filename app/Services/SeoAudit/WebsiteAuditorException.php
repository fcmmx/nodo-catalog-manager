<?php

namespace App\Services\SeoAudit;

class WebsiteAuditorException extends \RuntimeException
{
    public static function networkError(string $detail = ''): self
    {
        return new self('No se pudo descargar la página.'.($detail ? " Detalle: {$detail}" : ''));
    }

    public static function httpError(int $status): self
    {
        return new self("La página respondió con el código HTTP {$status} en vez de un contenido válido.");
    }
}
