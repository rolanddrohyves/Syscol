<?php
// app/Models/Configuration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuration extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot du modèle - pour vider le cache lors des modifications
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('app_configurations');
        });

        static::deleted(function () {
            Cache::forget('app_configurations');
        });
    }

    /**
     * Accesseur pour typer automatiquement la valeur
     */
    public function getValueAttribute($value)
    {
        return $this->castValue($value, $this->type);
    }

    /**
     * Mutateur pour s'assurer que la valeur est bien typée
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $this->serializeValue($value, $this->type ?? 'string');
    }

    /**
     * Récupère une configuration par sa clé
     */
    public static function get($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        
        if (!$config) {
            return $default;
        }
        
        return $config->value;
    }

    /**
     * Récupère plusieurs configurations d'un groupe
     */
    public static function getGroup($group)
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->key => $item->value];
            });
    }

    /**
     * Récupère toutes les configurations (avec cache)
     */
    public static function getAll($useCache = true)
    {
        if ($useCache) {
            return Cache::remember('app_configurations', 3600, function () {
                return self::all()->mapWithKeys(function ($item) {
                    return [$item->key => $item->value];
                });
            });
        }

        return self::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->value];
        });
    }

    /**
     * Définit une configuration
     */
    public static function set($key, $value, $group = 'general', $type = null, $description = null)
    {
        // Déterminer automatiquement le type si non spécifié
        if (is_null($type)) {
            $type = self::detectType($value);
        }

        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'description' => $description,
            ]
        );
    }

    /**
     * Définit plusieurs configurations à la fois
     */
    public static function setMany(array $configs, $group = 'general')
    {
        foreach ($configs as $key => $value) {
            self::set($key, $value, $group);
        }
    }

    /**
     * Vérifie si une configuration existe
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * Supprime une configuration
     */
    public static function remove($key)
    {
        return self::where('key', $key)->delete();
    }

    /**
     * Détecte le type d'une valeur
     */
    private static function detectType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        }
        if (is_int($value)) {
            return 'integer';
        }
        if (is_float($value)) {
            return 'float';
        }
        if (is_array($value) || is_object($value)) {
            return 'json';
        }
        if (is_null($value)) {
            return 'null';
        }
        return 'string';
    }

    /**
     * Convertit une valeur selon son type
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            case 'array':
                return json_decode($value, true) ?: [];
            case 'null':
                return null;
            default:
                return $value;
        }
    }

    /**
     * S'assure que la valeur est stockée correctement
     */
    private function serializeValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
            case 'array':
                return json_encode($value);
            case 'null':
                return null;
            default:
                return (string) $value;
        }
    }

    /**
     * Scope pour les configurations publiques
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope pour les configurations privées
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope pour un groupe spécifique
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}