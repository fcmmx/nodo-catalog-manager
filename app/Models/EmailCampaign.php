<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailCampaign extends Model
{
    use HasFactory, SoftDeletes;

    const TYPES = [
        'newsletter' => 'Newsletter',
        'lanzamiento' => 'Lanzamiento de producto',
        'promocion' => 'Promoción',
        'seguimiento' => 'Seguimiento',
        'bienvenida' => 'Bienvenida',
        'recuperacion' => 'Recuperación de prospectos',
        'reactivacion' => 'Reactivación de clientes',
        'recordatorio' => 'Recordatorio',
        'cotizacion' => 'Cotización',
        'confirmacion' => 'Confirmación',
    ];

    const STATUSES = [
        'borrador' => 'Borrador',
        'programada' => 'Programada',
        'enviando' => 'Enviando',
        'enviada' => 'Enviada',
        'pausada' => 'Pausada',
    ];

    const BLOCK_TYPES = [
        'header' => 'Encabezado',
        'text' => 'Texto',
        'image' => 'Imagen',
        'button' => 'Botón',
        'products' => 'Productos',
        'divider' => 'Separador',
        'social' => 'Redes sociales',
        'footer' => 'Pie legal',
    ];

    protected $fillable = [
        'name', 'type', 'subject', 'from_name', 'from_email', 'contact_list_id',
        'blocks', 'status', 'scheduled_at', 'sent_at', 'sent_count', 'open_count',
        'click_count', 'bounce_count', 'unsubscribe_count', 'batch_limit', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(ContactList::class, 'contact_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailCampaignSend::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function openRate(): float
    {
        return $this->sent_count > 0 ? round($this->open_count / $this->sent_count * 100, 1) : 0.0;
    }

    public function clickRate(): float
    {
        return $this->sent_count > 0 ? round($this->click_count / $this->sent_count * 100, 1) : 0.0;
    }
}
