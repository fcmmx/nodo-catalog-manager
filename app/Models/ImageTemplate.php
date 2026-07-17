<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageTemplate extends Model
{
    use HasFactory, SoftDeletes;

    const FORMATS = [
        'cuadrado' => ['label' => 'Cuadrado (1080×1080)', 'width' => 1080, 'height' => 1080],
        'vertical' => ['label' => 'Vertical (1080×1350)', 'width' => 1080, 'height' => 1350],
        'historia' => ['label' => 'Historia (1080×1920)', 'width' => 1080, 'height' => 1920],
        'horizontal' => ['label' => 'Horizontal (1200×628)', 'width' => 1200, 'height' => 628],
        'portada' => ['label' => 'Portada de colección (1920×1080)', 'width' => 1920, 'height' => 1080],
    ];

    protected $fillable = [
        'name', 'slug', 'format', 'width', 'height', 'background_type', 'background_value',
        'overlay_gradient', 'primary_color', 'accent_color', 'title_position',
        'show_price', 'show_qr', 'footer_text', 'is_master', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'overlay_gradient' => 'boolean',
            'show_price' => 'boolean',
            'show_qr' => 'boolean',
            'is_master' => 'boolean',
        ];
    }

    public function generations(): HasMany
    {
        return $this->hasMany(ImageGeneration::class, 'template_id');
    }
}
