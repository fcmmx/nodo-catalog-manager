<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportBatch extends Model
{
    protected $fillable = [
        'user_id', 'type', 'original_filename', 'stored_path', 'status',
        'total_rows', 'processed_rows', 'success_rows', 'error_rows',
        'duplicate_strategy', 'column_mapping', 'errors', 'errors_file_path',
        'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'column_mapping' => 'array',
            'errors' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
