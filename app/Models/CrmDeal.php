<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmDeal extends Model
{
    use HasFactory, SoftDeletes;

    const SOURCES = [
        'manual' => 'Manual',
        'landing' => 'Landing page',
        'importacion' => 'Importación',
    ];

    const STATUSES = [
        'abierto' => 'Abierto',
        'ganado' => 'Ganado',
        'perdido' => 'Perdido',
    ];

    protected $fillable = [
        'title', 'contact_id', 'product_id', 'stage_id', 'value', 'currency',
        'source', 'status', 'expected_close_date', 'lost_reason',
        'assigned_to', 'created_by', 'landing_lead_id',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmStage::class, 'stage_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function landingLead(): BelongsTo
    {
        return $this->belongsTo(LandingLead::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CrmActivity::class, 'deal_id')->latest();
    }

    public function formattedValue(): string
    {
        if ($this->value === null) {
            return '—';
        }

        return '$'.number_format((float) $this->value, 2).' '.$this->currency;
    }

    public function whatsappUrl(): ?string
    {
        $number = $this->contact->whatsapp ?: $this->contact->phone;
        if (! $number) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $number);

        return 'https://wa.me/'.$digits;
    }
}
