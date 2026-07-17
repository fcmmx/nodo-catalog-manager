<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    const STATUSES = ['borrador', 'activo', 'inactivo', 'archivado'];

    const AVAILABILITIES = ['disponible', 'agotado', 'bajo_pedido', 'proximamente'];

    const TYPES = ['producto', 'servicio'];

    protected $fillable = [
        'sku', 'name', 'short_name', 'slug', 'category_id', 'collection_id', 'type',
        'short_description', 'description', 'benefits', 'features',
        'price', 'old_price', 'currency', 'pricing_model', 'price_prefix_text', 'tax_included',
        'availability', 'status',
        'main_image', 'video_url', 'url', 'demo_url', 'whatsapp_url', 'whatsapp_message',
        'tags', 'keywords', 'seo_text', 'meta_title', 'meta_description', 'structured_data',
        'published_at', 'sort_order', 'is_featured', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'tax_included' => 'boolean',
            'is_featured' => 'boolean',
            'tags' => 'array',
            'structured_data' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::uniqueSlug(Str::slug($product->name));
            }
        });
    }

    public static function uniqueSlug(string $base): string
    {
        $slug = $base;
        $i = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'activo');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%");
        });
    }

    public function imageUrl(): ?string
    {
        if (! $this->main_image) {
            return null;
        }

        return Str::startsWith($this->main_image, ['http://', 'https://'])
            ? $this->main_image
            : \Illuminate\Support\Facades\Storage::disk('public')->url($this->main_image);
    }

    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return '—';
        }

        $prefix = $this->price_prefix_text ? $this->price_prefix_text.' ' : '';

        return $prefix.'$'.number_format((float) $this->price, 2).' '.$this->currency;
    }

    public function duplicate(): self
    {
        $copy = $this->replicate(['sku', 'slug']);
        $copy->sku = $this->sku.'-COPIA-'.Str::upper(Str::random(4));
        $copy->slug = static::uniqueSlug(Str::slug($this->name.'-copia'));
        $copy->name = $this->name.' (copia)';
        $copy->status = 'borrador';
        $copy->save();

        foreach ($this->images as $image) {
            $copy->images()->create([
                'path' => $image->path,
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
            ]);
        }

        return $copy;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'sku', 'status', 'price', 'availability'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
