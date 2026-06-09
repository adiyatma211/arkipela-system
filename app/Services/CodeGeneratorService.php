<?php

namespace App\Services;

use App\Enums\OrderDocumentType;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\Supplier;
use InvalidArgumentException;

class CodeGeneratorService
{
    public function generateSupplierCode(): string
    {
        $lastId = Supplier::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'SUP-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateClientCode(): string
    {
        $lastId = Client::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'CLI-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateOrderCode(): string
    {
        $lastId = Order::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'ORD-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateProductCode(): string
    {
        $lastId = Product::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'PRD-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateProductSkuCode(): string
    {
        $lastId = ProductSku::withTrashed()->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'SKU-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    public function generateOrderDocumentNumber(OrderDocumentType $documentType, int $documentId, ?int $year = null): string
    {
        if ($documentId < 1) {
            throw new InvalidArgumentException('Document ID must be greater than zero.');
        }

        $prefix = match ($documentType) {
            OrderDocumentType::COMMERCIAL_INVOICE => 'CI',
            OrderDocumentType::PACKING_LIST => 'PL',
        };

        return sprintf(
            '%s-%s-%05d',
            $prefix,
            $year ?? (int) now()->format('Y'),
            $documentId
        );
    }
}
