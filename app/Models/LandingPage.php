<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingPage extends Model
{
    use HasFactory, SoftDeletes;

    const STATUSES = [
        'borrador' => 'Borrador',
        'publicada' => 'Publicada',
        'archivada' => 'Archivada',
    ];

    const SECTION_TYPES = [
        'problema' => 'Problema',
        'solucion' => 'Solución',
        'beneficios' => 'Beneficios',
        'caracteristicas' => 'Características',
        'testimonios' => 'Testimonios',
        'faq' => 'Preguntas frecuentes',
        'producto' => 'Producto destacado',
        'texto' => 'Texto libre',
        'imagen' => 'Imagen',
        'video' => 'Video',
        'cta' => 'Llamada a la acción',
    ];

    protected $fillable = [
        'name', 'slug', 'product_id', 'status',
        'headline', 'subheadline', 'hero_image_path',
        'sections',
        'cta_text', 'cta_whatsapp_number', 'cta_whatsapp_message', 'cta_url',
        'meta_title', 'meta_description', 'og_image_path', 'structured_data',
        'ga4_id', 'meta_pixel_id', 'gtm_id',
        'capture_form_enabled', 'contact_list_id',
        'views_count', 'leads_count', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'sections' => 'array',
            'structured_data' => 'array',
            'capture_form_enabled' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LandingPage $landing) {
            if (empty($landing->slug)) {
                $landing->slug = static::uniqueSlug(Str::slug($landing->name));
            }
        });
    }

    public static function uniqueSlug(string $base): string
    {
        $base = $base ?: 'landing';
        $slug = $base;
        $i = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(LandingLead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function heroImageUrl(): ?string
    {
        return $this->resolveImageUrl($this->hero_image_path);
    }

    public function ogImageUrl(): ?string
    {
        return $this->resolveImageUrl($this->og_image_path) ?? $this->heroImageUrl();
    }

    protected function resolveImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : Storage::disk('public')->url($path);
    }

    public function publicUrl(): string
    {
        return route('landing.show', $this->slug);
    }

    public function isPublished(): bool
    {
        return $this->status === 'publicada';
    }

    public function conversionRate(): float
    {
        return $this->views_count > 0 ? round($this->leads_count / $this->views_count * 100, 1) : 0.0;
    }
}
