<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGeneration extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'task', 'provider', 'model',
        'prompt', 'response', 'input_tokens', 'output_tokens',
        'estimated_cost', 'status', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:4',
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
}
