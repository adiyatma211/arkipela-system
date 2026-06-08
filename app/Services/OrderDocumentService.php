<?php

namespace App\Services;

use App\Enums\OrderDocumentStatus;
use App\Enums\OrderDocumentType;
use App\Enums\SupplierPhotoType;
use App\Models\Document;
use App\Models\Order;

class OrderDocumentService
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly ArkipelaParameterService $arkipelaParameterService,
    ) {
    }

    public function generate(Document $document, Order $order, int $generatedBy): Document
    {
        $documentType = OrderDocumentType::from($document->document_type);
        $generatedAt = now();
        $documentNumber = $document->document_number
            ?: $this->codeGeneratorService->generateOrderDocumentNumber($documentType, $document->id, (int) $generatedAt->format('Y'));
        $snapshot = $this->buildSnapshot($order->fresh(['client', 'items.supplier.photos']), $documentType, $documentNumber, $generatedAt);

        $document->forceFill([
            'document_number' => $documentNumber,
            'status' => OrderDocumentStatus::GENERATED->value,
            'snapshot_payload' => $snapshot,
            'generated_at' => $generatedAt,
            'generated_by' => $generatedBy,
            'verified_at' => null,
            'verified_by' => null,
        ])->save();

        return $document->fresh(['generator', 'verifier']);
    }

    public function buildSnapshot(Order $order, OrderDocumentType $documentType, ?string $documentNumber = null, $generatedAt = null): array
    {
        $generatedAt = $generatedAt ?: now();
        $client = $order->client;
        $items = $order->items->values();
        $totalQuantityKg = (float) $items->sum(fn ($item) => (float) $item->quantity_kg);
        $totalQuantityPcs = (int) $items->sum(fn ($item) => (int) ($item->quantity_pcs ?? 0));
        $totalPackageCount = (int) $items->sum(fn ($item) => $this->resolvePackageCount($item));
        $totalNetWeightKg = (float) $items->sum(fn ($item) => $this->resolveNetWeightKg($item));
        $totalGrossWeightKg = (float) $items->sum(fn ($item) => $this->resolveGrossWeightKg($item));
        $totalCbm = (float) $items->sum(fn ($item) => $this->resolveTotalCbm($item));

        $snapshotItems = $items->map(function ($item, int $index) use ($order) {
            $packageCount = $this->resolvePackageCount($item);
            $cbmPerPackage = $this->resolveCbmPerPackage($item);
            $totalCbm = $this->resolveTotalCbm($item);
            $productPhotos = $this->resolveProductPhotos($item);

            return [
                'line_number' => $index + 1,
                'item_code' => $item->item_code,
                'supplier_name' => $item->supplier?->supplier_name,
                'product_name' => $item->product_name,
                'hs_code' => $item->hs_code,
                'specification' => $item->specification,
                'quantity_kg' => (float) $item->quantity_kg,
                'quantity_pcs' => $item->quantity_pcs ? (int) $item->quantity_pcs : null,
                'quantity_unit' => $item->quantity_unit ?: 'PCS',
                'pieces_per_package' => $item->pieces_per_package ? (int) $item->pieces_per_package : null,
                'package_count' => $packageCount ?: null,
                'package_type' => $item->package_type,
                'outer_package_type' => $item->outer_package_type,
                'length_cm' => $item->length_cm !== null ? (float) $item->length_cm : null,
                'width_cm' => $item->width_cm !== null ? (float) $item->width_cm : null,
                'height_cm' => $item->height_cm !== null ? (float) $item->height_cm : null,
                'dimension_unit' => $item->dimension_unit ?: 'CM',
                'net_weight_kg' => $this->resolveNetWeightKg($item),
                'gross_weight_kg' => $this->resolveGrossWeightKg($item),
                'cbm_per_package' => $cbmPerPackage > 0 ? $cbmPerPackage : null,
                'total_cbm' => $totalCbm > 0 ? $totalCbm : null,
                'package_notes' => $item->package_notes,
                'packaging_summary' => $this->buildPackagingSummary($item, $packageCount),
                'selling_price' => (float) $item->selling_price,
                'buying_price' => (float) $item->buying_price,
                'line_total_sales' => (float) $item->line_total_sales,
                'line_total_buying' => (float) $item->line_total_buying,
                'currency' => $order->currency,
                'product_photos' => $productPhotos,
            ];
        })->values();

        return [
            'document_type' => $documentType->value,
            'document_label' => $documentType->label(),
            'document_number' => $documentNumber,
            'generated_at' => $generatedAt->toIso8601String(),
            'seller' => [
                'company_name' => config('app.name', 'Archipela Web'),
                'country' => 'Indonesia',
            ],
            'buyer' => [
                'company_name' => $client?->company_name,
                'client_code' => $client?->client_code,
                'address' => $client?->address,
                'city' => $client?->city,
                'country' => $client?->country,
                'pic_name' => $client?->pic_name,
                'pic_email' => $client?->pic_email,
                'pic_whatsapp' => $client?->pic_whatsapp,
            ],
            'order' => [
                'order_code' => $order->order_code,
                'po_number' => $order->po_number,
                'order_date' => optional($order->order_date)->toDateString(),
                'delivery_date' => optional($order->delivery_date)->toDateString(),
                'destination_country' => $order->destination_country,
                'destination_port' => $order->destination_port,
                'shipment_mode' => $order->shipment_mode,
                'currency' => $order->currency,
                'incoterm' => $order->incoterm,
                'payment_term' => $order->payment_term,
                'notes' => $order->notes,
            ],
            'items' => $snapshotItems->all(),
            'photo_attachments' => $snapshotItems
                ->filter(fn ($item) => ! empty($item['product_photos']))
                ->map(fn ($item) => [
                    'line_number' => $item['line_number'],
                    'item_code' => $item['item_code'],
                    'product_name' => $item['product_name'],
                    'supplier_name' => $item['supplier_name'],
                    'photos' => $item['product_photos'],
                ])
                ->values()
                ->all(),
            'totals' => [
                'line_item_count' => $items->count(),
                'total_quantity_kg' => round($totalQuantityKg, 2),
                'total_quantity_pcs' => $totalQuantityPcs,
                'total_package_count' => $totalPackageCount,
                'total_net_weight_kg' => round($totalNetWeightKg, 2),
                'total_gross_weight_kg' => round($totalGrossWeightKg, 2),
                'total_cbm' => round($totalCbm, 4),
                'subtotal_sales' => (float) $order->subtotal_sales,
                'subtotal_buying' => (float) $order->subtotal_buying,
                'gross_profit' => (float) $order->gross_profit,
                'total_additional_cost' => (float) $order->total_additional_cost,
                'net_profit' => (float) $order->net_profit,
            ],
            'packing_summary' => [
                'line_item_count' => $items->count(),
                'total_quantity_kg' => round($totalQuantityKg, 2),
                'total_quantity_pcs' => $totalQuantityPcs,
                'total_package_count' => $totalPackageCount,
                'total_net_weight_kg' => round($totalNetWeightKg, 2),
                'total_gross_weight_kg' => round($totalGrossWeightKg, 2),
                'total_cbm' => round($totalCbm, 4),
                'shipment_mode' => $order->shipment_mode,
                'destination_port' => $order->destination_port,
                'notes' => $order->notes,
            ],
        ];
    }

    private function resolvePackageCount($item): int
    {
        if (! empty($item->package_count)) {
            return (int) $item->package_count;
        }

        if (! empty($item->quantity_pcs) && ! empty($item->pieces_per_package)) {
            return (int) ceil(((int) $item->quantity_pcs) / ((int) $item->pieces_per_package));
        }

        return 0;
    }

    private function resolveNetWeightKg($item): float
    {
        return round((float) ($item->net_weight_kg ?? $item->quantity_kg ?? 0), 2);
    }

    private function resolveGrossWeightKg($item): float
    {
        return round((float) ($item->gross_weight_kg ?? $item->net_weight_kg ?? $item->quantity_kg ?? 0), 2);
    }

    private function resolveCbmPerPackage($item): float
    {
        $length = (float) ($item->length_cm ?? 0);
        $width = (float) ($item->width_cm ?? 0);
        $height = (float) ($item->height_cm ?? 0);

        if ($length <= 0 || $width <= 0 || $height <= 0) {
            return 0;
        }

        $factor = $this->arkipelaParameterService->dimensionToCmFactor($item->dimension_unit ?? 'CM');
        $lengthInCm = $length * $factor;
        $widthInCm = $width * $factor;
        $heightInCm = $height * $factor;

        return round(($lengthInCm * $widthInCm * $heightInCm) / 1000000, 4);
    }

    private function resolveTotalCbm($item): float
    {
        $cbmPerPackage = $this->resolveCbmPerPackage($item);
        $packageCount = $this->resolvePackageCount($item);

        if ($cbmPerPackage <= 0 || $packageCount <= 0) {
            return 0;
        }

        return round($cbmPerPackage * $packageCount, 4);
    }

    private function buildPackagingSummary($item, int $packageCount): ?string
    {
        $segments = [];
        $quantityUnit = $item->quantity_unit ?: 'PCS';

        if (! empty($item->pieces_per_package) && ! empty($item->package_type)) {
            $segments[] = "{$item->pieces_per_package} {$quantityUnit} / {$item->package_type}";
        }

        if ($packageCount > 0 && ! empty($item->package_type)) {
            $segments[] = "{$packageCount} {$item->package_type}";
        }

        if (! empty($item->outer_package_type)) {
            $segments[] = "outer: {$item->outer_package_type}";
        }

        if (! empty($item->package_notes)) {
            $segments[] = $item->package_notes;
        }

        return $segments !== [] ? implode(' | ', $segments) : null;
    }

    private function resolveProductPhotos($item): array
    {
        $photos = $item->supplier?->photos ?? collect();

        $preferred = $photos
            ->where('photo_type', SupplierPhotoType::PRODUCT->value)
            ->sortBy('sort_order')
            ->values();

        if ($preferred->isEmpty()) {
            $preferred = $photos
                ->sortBy('sort_order')
                ->values();
        }

        return $preferred
            ->take(6)
            ->map(fn ($photo) => [
                'photo_type' => $photo->photo_type,
                'caption' => $photo->caption,
                'url' => $photo->photoUrl(),
            ])
            ->all();
    }
}
