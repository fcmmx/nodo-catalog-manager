<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SocialPost extends Model
{
    use HasFactory, SoftDeletes;

    const CHANNELS = ['facebook', 'instagram', 'linkedin', 'tiktok', 'x', 'google_business'];

    const STATUSES = [
        'borrador' => 'Borrador',
        'programada' => 'Programada',
        'enviando' => 'Enviando',
        'enviada' => 'Enviada',
        'pendiente_autorizacion' => 'Pendiente de autorización',
        'error' => 'Con error',
        'publicada_manual' => 'Publicada manualmente',
        'cancelada' => 'Cancelada',
    ];

    protected $fillable = [
        'user_id', 'product_id', 'social_account_id', 'channel', 'content',
        'image_path', 'video_path', 'hashtags', 'link', 'scheduled_at', 'timezone',
        'status', 'result', 'external_post_id', 'error_message', 'duplicated_from',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class, 'social_account_id');
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['borrador', 'programada', 'pendiente_autorizacion', 'error']);
    }
}
