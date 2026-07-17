<?php

namespace App\Services\Social\Exceptions;

class SocialPublishException extends \RuntimeException
{
    public const REASON_NOT_AUTHORIZED = 'not_authorized';

    public const REASON_UNSUPPORTED_CHANNEL = 'unsupported_channel';

    public const REASON_TOKEN_EXPIRED = 'token_expired';

    public const REASON_API_ERROR = 'api_error';

    public const REASON_NETWORK_ERROR = 'network_error';

    public function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }

    public static function notAuthorized(): self
    {
        return new self(
            self::REASON_NOT_AUTHORIZED,
            'Esta publicación no tiene una cuenta autorizada. Conecta la cuenta en Redes Sociales → Cuentas antes de publicar automáticamente, o descarga el contenido y publícalo manualmente.'
        );
    }

    public static function unsupportedChannel(string $channel): self
    {
        return new self(
            self::REASON_UNSUPPORTED_CHANNEL,
            "La publicación automática para {$channel} todavía no está disponible en este sistema. Descarga el contenido y márcalo como \"publicado manualmente\" cuando lo publiques tú mismo."
        );
    }

    public static function tokenExpired(): self
    {
        return new self(
            self::REASON_TOKEN_EXPIRED,
            'El token de acceso de esta cuenta expiró o fue revocado. Vuelve a conectar la cuenta en Redes Sociales → Cuentas.'
        );
    }

    public static function apiError(string $detail = ''): self
    {
        return new self(
            self::REASON_API_ERROR,
            'La plataforma rechazó la publicación.'.($detail ? " Detalle: {$detail}" : '')
        );
    }

    public static function networkError(string $detail = ''): self
    {
        return new self(
            self::REASON_NETWORK_ERROR,
            'No se pudo conectar con la plataforma.'.($detail ? " Detalle: {$detail}" : '')
        );
    }
}
