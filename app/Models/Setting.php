<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'is_encrypted'];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            if (! $row) {
                return $default;
            }

            return $row->is_encrypted && $row->value !== null
                ? Crypt::decryptString($row->value)
                : $row->value;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general', bool $encrypted = false): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'value' => $encrypted && $value !== null ? Crypt::encryptString((string) $value) : $value,
                'is_encrypted' => $encrypted,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    public static function group(string $group): array
    {
        return static::where('group', $group)->get()
            ->mapWithKeys(fn ($row) => [
                $row->key => $row->is_encrypted && $row->value !== null
                    ? Crypt::decryptString($row->value)
                    : $row->value,
            ])->toArray();
    }
}
