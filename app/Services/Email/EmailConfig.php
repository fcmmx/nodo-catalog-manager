<?php

namespace App\Services\Email;

use App\Models\Setting;

/**
 * Configuración del proveedor de envío para email marketing. Es
 * independiente del SMTP transaccional de .env (recuperación de
 * contraseña) para no interferir con él. Cubre SMTP genérico y, mediante
 * el mismo mecanismo, Brevo/Mailgun/SendGrid/Amazon SES a través de sus
 * respectivas interfaces SMTP (todas exponen una, sin necesidad de
 * dependencias extra por proveedor).
 */
class EmailConfig
{
    public function enabled(): bool
    {
        return Setting::get('email_marketing_enabled', '0') === '1';
    }

    public function provider(): string
    {
        return Setting::get('email_marketing_provider', 'smtp');
    }

    public function host(): ?string
    {
        return Setting::get('email_marketing_host');
    }

    public function port(): int
    {
        return (int) Setting::get('email_marketing_port', 587);
    }

    public function username(): ?string
    {
        return Setting::get('email_marketing_username');
    }

    public function password(): ?string
    {
        return Setting::get('email_marketing_password');
    }

    public function encryption(): ?string
    {
        return Setting::get('email_marketing_encryption', 'tls');
    }

    public function fromName(): string
    {
        return Setting::get('email_marketing_from_name', 'NODO 360 MARKETING TECHNOLOGY');
    }

    public function fromEmail(): string
    {
        return Setting::get('email_marketing_from_email', 'info@nodo360mkt.site');
    }

    public function isConfigured(): bool
    {
        return $this->enabled() && ! empty($this->host()) && ! empty($this->username());
    }

    /**
     * Registra en tiempo de ejecución un mailer SMTP dedicado a campañas,
     * sin afectar el mailer transaccional configurado en .env.
     */
    public function applyRuntimeConfig(): void
    {
        config([
            'mail.mailers.campaign_smtp' => [
                'transport' => 'smtp',
                'host' => $this->host(),
                'port' => $this->port(),
                'encryption' => $this->encryption() ?: null,
                'username' => $this->username(),
                'password' => $this->password(),
                'timeout' => 30,
            ],
        ]);
    }

    public function mailerName(): string
    {
        return 'campaign_smtp';
    }
}
