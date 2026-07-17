<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestConnectionMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $fromName)
    {
    }

    public function build(): static
    {
        return $this
            ->subject('Prueba de conexión — NODO Catalog Manager')
            ->html('<p>Este es un correo de prueba enviado desde <strong>'.e($this->fromName).'</strong> mediante NODO Catalog Manager. Si lo recibiste, la configuración de email marketing funciona correctamente.</p>');
    }
}
