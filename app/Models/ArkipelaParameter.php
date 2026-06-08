<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ArkipelaParameter extends Model
{
    protected $fillable = [
        'group_key',
        'code',
        'name',
        'description',
        'attributes',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeGroup(Builder $query, string $groupKey): Builder
    {
        return $query->where('group_key', $groupKey);
    }
}
