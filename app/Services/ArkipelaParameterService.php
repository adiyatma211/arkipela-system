<?php

namespace App\Services;

use App\Models\ArkipelaParameter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ArkipelaParameterService
{
    public const GROUP_QUANTITY_UNIT = 'quantity_unit';
    public const GROUP_DIMENSION_UNIT = 'dimension_unit';
    public const GROUP_PACKAGING_TYPE = 'packaging_type';
    public const GROUP_OUTER_PACKAGING_TYPE = 'outer_packaging_type';

    public function options(string $groupKey): array
    {
        return $this->values($groupKey)
            ->map(fn (ArkipelaParameter $parameter) => [
                'value' => $parameter->code,
                'label' => $parameter->name,
                'description' => $parameter->description,
            ])
            ->values()
            ->all();
    }

    public function values(string $groupKey): Collection
    {
        return Cache::remember(
            "arkipela-parameters:{$groupKey}",
            now()->addMinutes(30),
            fn () => ArkipelaParameter::query()
                ->group($groupKey)
                ->active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function dimensionToCmFactor(?string $unitCode): float
    {
        $normalized = strtoupper(trim((string) $unitCode));

        if ($normalized === '') {
            return 1.0;
        }

        $parameter = $this->values(self::GROUP_DIMENSION_UNIT)
            ->first(fn (ArkipelaParameter $row) => strtoupper($row->code) === $normalized);

        $factor = data_get($parameter?->attributes, 'to_cm_factor');

        if (is_numeric($factor)) {
            return (float) $factor;
        }

        return 1.0;
    }
}
