<?php

namespace App\Services\AI\Exceptions;

class AiException extends \RuntimeException
{
    public const REASON_NOT_CONFIGURED = 'not_configured';

    public const REASON_INVALID_TOKEN = 'invalid_token';

    public const REASON_QUOTA_EXCEEDED = 'quota_exceeded';

    public const REASON_RATE_LIMITED = 'rate_limited';

    public const REASON_NETWORK_ERROR = 'network_error';

    public const REASON_UNKNOWN = 'unknown';

    public function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }

    public static function notConfigured(): self
    {
        return new self(
            self::REASON_NOT_CONFIGURED,
            'No hay un proveedor de inteligencia artificial configurado. Ve a Configuración → IA y guarda una clave de API válida antes de usar esta función.'
        );
    }

    public static function invalidToken(string $detail = ''): self
    {
        return new self(
            self::REASON_INVALID_TOKEN,
            'El proveedor de IA rechazó la clave de API configurada (token inválido o revocado).'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function quotaExceeded(string $detail = ''): self
    {
        return new self(
            self::REASON_QUOTA_EXCEEDED,
            'Se agotó la cuota o el saldo disponible en la cuenta del proveedor de IA.'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function rateLimited(string $detail = ''): self
    {
        return new self(
            self::REASON_RATE_LIMITED,
            'El proveedor de IA limitó las solicitudes por exceso de uso momentáneo. Intenta de nuevo en unos segundos.'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function networkError(string $detail = ''): self
    {
        return new self(
            self::REASON_NETWORK_ERROR,
            'No se pudo establecer conexión con el proveedor de IA.'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function unknown(string $detail = ''): self
    {
        return new self(
            self::REASON_UNKNOWN,
            'El proveedor de IA devolvió un error inesperado.'.($detail ? " Detalle: {$detail}" : '')
        );
    }
}
