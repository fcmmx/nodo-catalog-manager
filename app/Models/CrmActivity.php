<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    use HasFactory;

    const TYPES = [
        'nota' => 'Nota',
        'llamada' => 'Llamada',
        'reunion' => 'Reunión',
        'tarea' => 'Tarea / recordatorio',
        'whatsapp' => 'WhatsApp',
    ];

    protected $fillable = ['deal_id', 'user_id', 'type', 'content', 'due_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(CrmDeal::class, 'deal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isReminder(): bool
    {
        return $this->type === 'tarea' && $this->due_at !== null;
    }

    public function isOverdue(): bool
    {
        return $this->isReminder() && ! $this->completed_at && $this->due_at->isPast();
    }
}
