<?php

namespace App\Services;

use App\Enums\OrderDocumentStatus;
use App\Enums\OrderDocumentType;
use App\Models\Document;
use App\Models\Order;

class OrderDocumentService
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
    ) {
    }

    public function generate(Document $document, Order $order, int $generatedBy): Document
    {
        $documentType = OrderDocumentType::from($document->document_type);
        $generatedAt = now();
        $documentNumber = $document->document_number
            ?: $this->codeGeneratorService->generateOrderDocumentNumber($documentType, $document->id, (int) $generatedAt->format('Y'));
        $snapshot = $this->buildSnapshot($order->fresh(['client', 'items.supplier']), $documentType, $documentNumber, $generatedAt);

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
            'items' => $items->map(function ($item, int $index) use ($order) {
                return [
                    'line_number' => $index + 1,
                    'supplier_name' => $item->supplier?->supplier_name,
                    'product_name' => $item->product_name,
                    'specification' => $item->specification,
                    'quantity_kg' => (float) $item->quantity_kg,
                    'selling_price' => (float) $item->selling_price,
                    'buying_price' => (float) $item->buying_price,
                    'line_total_sales' => (float) $item->line_total_sales,
                    'line_total_buying' => (float) $item->line_total_buying,
                    'currency' => $order->currency,
                ];
            })->all(),
            'totals' => [
                'line_item_count' => $items->count(),
                'total_quantity_kg' => round($totalQuantityKg, 2),
                'subtotal_sales' => (float) $order->subtotal_sales,
                'subtotal_buying' => (float) $order->subtotal_buying,
                'gross_profit' => (float) $order->gross_profit,
                'total_additional_cost' => (float) $order->total_additional_cost,
                'net_profit' => (float) $order->net_profit,
            ],
            'packing_summary' => [
                'line_item_count' => $items->count(),
                'total_quantity_kg' => round($totalQuantityKg, 2),
                'shipment_mode' => $order->shipment_mode,
                'destination_port' => $order->destination_port,
                'notes' => $order->notes,
            ],
        ];
    }
}
