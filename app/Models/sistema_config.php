<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class sistema_config extends Model
{
    protected $table = 'sistema_config';
    protected $primaryKey = 'sistema_config_id';

    protected $fillable = ['clave', 'valor', 'descripcion', 'updated_by'];

    public static function valor(string $clave, ?string $default = null): ?string
    {
        return Cache::remember("sistema_config_{$clave}", 3600, function () use ($clave, $default) {
            $row = static::where('clave', $clave)->first();

            return $row?->valor ?? $default;
        });
    }

    public static function valorFloat(string $clave, float $default = 0.0): float
    {
        $v = static::valor($clave, (string) $default);

        return is_numeric($v) ? (float) $v : $default;
    }

    public static function guardar(string $clave, string $valor, ?int $userId = null, ?string $descripcion = null): void
    {
        static::updateOrCreate(
            ['clave' => $clave],
            array_filter([
                'valor' => $valor,
                'descripcion' => $descripcion,
                'updated_by' => $userId,
            ], fn ($v) => $v !== null)
        );

        Cache::forget("sistema_config_{$clave}");
    }
}