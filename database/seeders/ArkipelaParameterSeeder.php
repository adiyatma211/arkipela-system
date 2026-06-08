<?php

namespace Database\Seeders;

use App\Models\ArkipelaParameter;
use App\Services\ArkipelaParameterService;
use Illuminate\Database\Seeder;

class ArkipelaParameterSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rows = [
            [ArkipelaParameterService::GROUP_QUANTITY_UNIT, 'PCS', 'Pieces', 'Individual piece count', 10, null],
            [ArkipelaParameterService::GROUP_QUANTITY_UNIT, 'SET', 'Set', 'Grouped set quantity', 20, null],
            [ArkipelaParameterService::GROUP_QUANTITY_UNIT, 'UNIT', 'Unit', 'General unit count', 30, null],
            [ArkipelaParameterService::GROUP_QUANTITY_UNIT, 'PAIR', 'Pair', 'Sold or packed by pair', 40, null],

            [ArkipelaParameterService::GROUP_DIMENSION_UNIT, 'CM', 'Centimeter', 'Standard carton dimension in cm', 10, ['to_cm_factor' => 1]],
            [ArkipelaParameterService::GROUP_DIMENSION_UNIT, 'MM', 'Millimeter', 'Millimeter dimension input', 20, ['to_cm_factor' => 0.1]],
            [ArkipelaParameterService::GROUP_DIMENSION_UNIT, 'M', 'Meter', 'Meter dimension input', 30, ['to_cm_factor' => 100]],
            [ArkipelaParameterService::GROUP_DIMENSION_UNIT, 'IN', 'Inch', 'Imperial inch dimension input', 40, ['to_cm_factor' => 2.54]],

            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'BOX', 'Box', 'Individual product box', 10, null],
            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'CARTON', 'Carton', 'Master carton packaging', 20, null],
            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'BAG', 'Bag', 'Bag packaging', 30, null],
            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'BALE', 'Bale', 'Baled packing', 40, null],
            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'DRUM', 'Drum', 'Drum packaging', 50, null],
            [ArkipelaParameterService::GROUP_PACKAGING_TYPE, 'PALLET', 'Pallet', 'Palletized product', 60, null],

            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'NONE', 'None', 'No outer packaging', 10, null],
            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'WOODEN_CRATE', 'Wooden Crate', 'Packed in wooden crate', 20, null],
            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'WOODEN_FRAME', 'Wooden Frame', 'Reinforced with wooden frame', 30, null],
            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'PALLET_WRAP', 'Pallet Wrap', 'Wrapped on pallet', 40, null],
            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'SHRINK_WRAP', 'Shrink Wrap', 'Shrink wrapped outer packaging', 50, null],
            [ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE, 'BUBBLE_WRAP', 'Bubble Wrap', 'Outer protective wrap', 60, null],
        ];

        foreach ($rows as [$groupKey, $code, $name, $description, $sortOrder, $attributes]) {
            ArkipelaParameter::query()->updateOrCreate(
                [
                    'group_key' => $groupKey,
                    'code' => $code,
                ],
                [
                    'name' => $name,
                    'description' => $description,
                    'attributes' => $attributes,
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );
        }
    }
}
