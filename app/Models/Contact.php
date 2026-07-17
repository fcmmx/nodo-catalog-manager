<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'company', 'phone', 'whatsapp', 'email', 'source', 'tags',
        'consent', 'consent_at', 'subscribed', 'unsubscribed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'consent' => 'boolean',
            'consent_at' => 'datetime',
            'subscribed' => 'boolean',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(ContactList::class, 'contact_list_contact');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailCampaignSend::class);
    }

    public function unsubscribe(): void
    {
        $this->update(['subscribed' => false, 'unsubscribed_at' => now()]);
    }
}
