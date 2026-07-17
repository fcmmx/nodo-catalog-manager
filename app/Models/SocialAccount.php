<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class SocialAccount extends Model
{
    use HasFactory, SoftDeletes;

    const CHANNELS = ['facebook', 'instagram', 'linkedin', 'tiktok', 'x', 'google_business'];

    protected $fillable = [
        'channel', 'label', 'external_account_id', 'access_token',
        'token_expires_at', 'is_active', 'created_by',
    ];

    protected $hidden = ['access_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'token_expires_at' => 'datetime',
        ];
    }

    public function setAccessTokenAttribute(?string $value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getDecryptedAccessTokenAttribute(): ?string
    {
        if (! $this->attributes['access_token']) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['access_token']);
        } catch (\Exception) {
            return null;
        }
    }

    public function isAuthorized(): bool
    {
        return ! empty($this->attributes['access_token'])
            && (! $this->token_expires_at || $this->token_expires_at->isFuture());
    }

    public function posts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }
}
