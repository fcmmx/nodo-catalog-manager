<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id', 'contact_id', 'name', 'email', 'phone', 'message',
        'utm_source', 'utm_medium', 'utm_campaign', 'ip_address',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
