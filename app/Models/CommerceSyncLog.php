<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommerceSyncLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['type', 'status', 'products_count', 'message', 'ip_address'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
