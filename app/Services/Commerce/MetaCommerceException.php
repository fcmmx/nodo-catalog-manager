<?php

namespace App\Services\Commerce;

class MetaCommerceException extends \RuntimeException
{
    public const REASON_NOT_CONFIGURED = 'not_configured';

    public const REASON_INVALID_TOKEN = 'invalid_token';

    public const REASON_API_ERROR = 'api_error';

    public const REASON_NETWORK_ERROR = 'network_error';

    public function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }

    public static function notConfigured(): self
    {
        return new self(
            self::REASON_NOT_CONFIGURED,
            'Configura el ID de catálogo y el token de acceso de Meta antes de probar la conexión.'
        );
    }

    public static function invalidToken(): self
    {
        return new self(
            self::REASON_INVALID_TOKEN,
            'El token de acceso es inválido o expiró. Genera uno nuevo desde Meta Business Suite.'
        );
    }

    public static function apiError(string $detail = ''): self
    {
        return new self(
            self::REASON_API_ERROR,
            'Meta rechazó la solicitud.'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function networkError(string $detail = ''): self
    {
        return new self(
            self::REASON_NETWORK_ERROR,
            'No se pudo conectar con la Graph API de Meta.'.($detail ? " Detalle: {$detail}" : '')
        );
    }
}
