<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return match ($setting->type) {
                'array' => json_decode($setting->value, true),
                'boolean' => (bool) $setting->value,
                default => $setting->value,
            };
        });
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $settingValue = ($type === 'array') ? json_encode($value) : (string) $value;

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $settingValue, 'type' => $type]
        );

        Cache::forget("setting.{$key}");
    }
}
