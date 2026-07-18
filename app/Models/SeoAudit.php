<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoAudit extends Model
{
    protected $fillable = ['url', 'status', 'score', 'seo_score', 'aeo_score', 'geo_score', 'results', 'error_message', 'created_by'];

    protected function casts(): array
    {
        return [
            'results' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grade(): string
    {
        return match (true) {
            $this->score === null => '—',
            $this->score >= 90 => 'A',
            $this->score >= 75 => 'B',
            $this->score >= 60 => 'C',
            $this->score >= 40 => 'D',
            default => 'F',
        };
    }
}
