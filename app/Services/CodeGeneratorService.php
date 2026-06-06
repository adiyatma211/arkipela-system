<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;

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
}
