<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\SupplierStatus;
use App\Http\Requests\OrderRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'client_id' => $request->string('client_id')->toString(),
        ];

        $orders = Order::query()
            ->with(['client', 'items.supplier'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('order_code', 'like', "%{$search}%")
                        ->orWhere('po_number', 'like', "%{$search}%")
                        ->orWhereHas('client', fn ($clientQuery) => $clientQuery->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['client_id'], fn ($query, $clientId) => $query->where('client_id', $clientId))
            ->latest('order_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', [
            'pageTitle' => 'Order Management',
            'pageSubtitle' => 'Kelola transaksi client, item order, supplier sourcing, dan gross margin dalam satu flow.',
            'orders' => $orders,
            'filters' => $filters,
            'statusOptions' => OrderStatus::options(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'clientOptions' => Client::query()->orderBy('company_name')->get(['id', 'company_name', 'client_code']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('orders.create', [
            'pageTitle' => 'Create Order',
            'pageSubtitle' => 'Buat order baru dan hitung gross profit langsung dari item lines.',
            'order' => new Order([
                'order_date' => now()->toDateString(),
                'currency' => 'USD',
                'status' => OrderStatus::DRAFT->value,
                'destination_country' => 'United States',
                'shipment_mode' => 'FCL',
            ]),
            'clients' => Client::query()->orderBy('company_name')->get(['id', 'company_name', 'client_code']),
            'suppliers' => $this->availableSuppliers(),
            'statusOptions' => OrderStatus::options(),
            'formAction' => route('orders.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Order',
            'itemsData' => [$this->emptyItem()],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $itemsPayload = $this->normalizeItems($payload['items']);
        $totals = $this->calculateTotals($itemsPayload, $payload);

        $payload['order_code'] = $this->codeGeneratorService->generateOrderCode();
        $payload['created_by'] = $request->user()?->id;
        $payload['subtotal_sales'] = $totals['subtotal_sales'];
        $payload['subtotal_buying'] = $totals['subtotal_buying'];
        $payload['gross_profit'] = $totals['gross_profit'];
        $payload['gross_margin'] = $totals['gross_margin'];
        $payload['total_additional_cost'] = $totals['total_additional_cost'];
        $payload['net_profit'] = $totals['net_profit'];
        $payload['net_margin'] = $totals['net_margin'];
        $payload['confirmed_at'] = in_array($payload['status'], [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PRODUCTION->value,
            OrderStatus::READY_TO_SHIP->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ], true) ? now() : null;

        unset($payload['items']);

        $order = Order::query()->create($payload);
        $order->items()->createMany($itemsPayload);

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $order,
            action: 'created',
            newValue: $order->load(['client', 'items'])->toArray(),
            description: "Order {$order->order_code} created",
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('status', "Order {$order->order_code} created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        return view('orders.show', [
            'pageTitle' => 'Order Detail',
            'pageSubtitle' => 'Pantau header order, item sourcing, dan margin transaksi.',
            'order' => $order->load(['client', 'creator', 'items.supplier']),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order): View
    {
        return view('orders.edit', [
            'pageTitle' => 'Edit Order',
            'pageSubtitle' => "Update data {$order->order_code} dan recalculate margin bila ada perubahan item.",
            'order' => $order->load('items'),
            'clients' => Client::query()->orderBy('company_name')->get(['id', 'company_name', 'client_code']),
            'suppliers' => $this->availableSuppliers(),
            'statusOptions' => OrderStatus::options(),
            'formAction' => route('orders.update', $order),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Order',
            'itemsData' => $order->items
                ->map(fn ($item) => [
                    'supplier_id' => $item->supplier_id,
                    'product_name' => $item->product_name,
                    'specification' => $item->specification,
                    'quantity_kg' => $item->quantity_kg,
                    'selling_price' => $item->selling_price,
                    'buying_price' => $item->buying_price,
                ])
                ->values()
                ->all() ?: [$this->emptyItem()],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderRequest $request, Order $order): RedirectResponse
    {
        $oldValue = $order->load(['client', 'items'])->toArray();
        $payload = $request->validated();
        $itemsPayload = $this->normalizeItems($payload['items']);
        $totals = $this->calculateTotals($itemsPayload, $payload);

        $payload['subtotal_sales'] = $totals['subtotal_sales'];
        $payload['subtotal_buying'] = $totals['subtotal_buying'];
        $payload['gross_profit'] = $totals['gross_profit'];
        $payload['gross_margin'] = $totals['gross_margin'];
        $payload['total_additional_cost'] = $totals['total_additional_cost'];
        $payload['net_profit'] = $totals['net_profit'];
        $payload['net_margin'] = $totals['net_margin'];
        $payload['confirmed_at'] = in_array($payload['status'], [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PRODUCTION->value,
            OrderStatus::READY_TO_SHIP->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ], true) ? ($order->confirmed_at ?? now()) : null;

        unset($payload['items']);

        $order->update($payload);
        $order->items()->delete();
        $order->items()->createMany($itemsPayload);

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $order,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $order->fresh()->load(['client', 'items'])->toArray(),
            description: "Order {$order->order_code} updated",
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('status', "Order {$order->order_code} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $oldValue = $order->load(['client', 'items'])->toArray();
        $orderCode = $order->order_code;
        $order->delete();

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $order,
            action: 'deleted',
            oldValue: $oldValue,
            description: "Order {$orderCode} deleted",
        );

        return redirect()
            ->route('orders.index')
            ->with('status', "Order {$orderCode} deleted successfully.");
    }

    private function availableSuppliers()
    {
        return Supplier::query()
            ->with('products')
            ->whereIn('status', [
                SupplierStatus::APPROVED->value,
                SupplierStatus::ACTIVE->value,
            ])
            ->orderBy('supplier_name')
            ->get(['id', 'supplier_name', 'supplier_code', 'status', 'products_summary', 'monthly_capacity_kg', 'minimum_order_kg']);
    }

    private function emptyItem(): array
    {
        return [
            'supplier_id' => null,
            'product_name' => '',
            'specification' => '',
            'quantity_kg' => null,
            'selling_price' => null,
            'buying_price' => null,
        ];
    }

    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->map(function (array $item) {
                $quantity = (float) $item['quantity_kg'];
                $sellingPrice = (float) $item['selling_price'];
                $buyingPrice = (float) $item['buying_price'];
                $lineTotalSales = $quantity * $sellingPrice;
                $lineTotalBuying = $quantity * $buyingPrice;

                return [
                    'supplier_id' => $item['supplier_id'] ?: null,
                    'product_name' => $item['product_name'],
                    'specification' => $item['specification'] ?: null,
                    'quantity_kg' => round($quantity, 2),
                    'selling_price' => round($sellingPrice, 2),
                    'buying_price' => round($buyingPrice, 2),
                    'line_total_sales' => round($lineTotalSales, 2),
                    'line_total_buying' => round($lineTotalBuying, 2),
                    'line_profit' => round($lineTotalSales - $lineTotalBuying, 2),
                ];
            })
            ->values()
            ->all();
    }

    private function calculateTotals(array $items, array $payload): array
    {
        $subtotalSales = collect($items)->sum('line_total_sales');
        $subtotalBuying = collect($items)->sum('line_total_buying');
        $grossProfit = $subtotalSales - $subtotalBuying;
        $grossMargin = $subtotalSales > 0 ? ($grossProfit / $subtotalSales) * 100 : 0;
        $totalAdditionalCost = collect([
            $payload['local_logistics_cost'] ?? 0,
            $payload['export_document_cost'] ?? 0,
            $payload['forwarding_cost'] ?? 0,
            $payload['freight_cost'] ?? 0,
            $payload['insurance_cost'] ?? 0,
            $payload['compliance_cost'] ?? 0,
            $payload['destination_cost'] ?? 0,
            $payload['misc_cost'] ?? 0,
        ])->sum(fn ($value) => (float) $value);
        $netProfit = $grossProfit - $totalAdditionalCost;
        $netMargin = $subtotalSales > 0 ? ($netProfit / $subtotalSales) * 100 : 0;

        return [
            'subtotal_sales' => round($subtotalSales, 2),
            'subtotal_buying' => round($subtotalBuying, 2),
            'gross_profit' => round($grossProfit, 2),
            'gross_margin' => round($grossMargin, 2),
            'total_additional_cost' => round($totalAdditionalCost, 2),
            'net_profit' => round($netProfit, 2),
            'net_margin' => round($netMargin, 2),
        ];
    }

    private function statusBadgeMap(): array
    {
        return [
            OrderStatus::DRAFT->value => 'bg-light-secondary',
            OrderStatus::QUOTATION->value => 'bg-light-info',
            OrderStatus::CONFIRMED->value => 'bg-primary',
            OrderStatus::PRODUCTION->value => 'bg-warning text-dark',
            OrderStatus::READY_TO_SHIP->value => 'bg-info',
            OrderStatus::SHIPPED->value => 'bg-success',
            OrderStatus::COMPLETED->value => 'bg-success',
            OrderStatus::CANCELLED->value => 'bg-danger',
        ];
    }

    private function statusLabelMap(): array
    {
        return collect(OrderStatus::options())
            ->pluck('label', 'value')
            ->all();
    }
}
