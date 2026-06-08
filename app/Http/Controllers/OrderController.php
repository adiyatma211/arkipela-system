<?php

namespace App\Http\Controllers;

use App\Enums\OrderDocumentStatus;
use App\Enums\OrderDocumentType;
use App\Enums\OrderStatus;
use App\Enums\SupplierApprovalStatus;
use App\Enums\SupplierStatus;
use App\Http\Requests\OrderRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\Order;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use App\Services\ArkipelaParameterService;
use App\Services\CodeGeneratorService;
use App\Services\OrderDocumentService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly OrderDocumentService $orderDocumentService,
        private readonly ActivityLogService $activityLogService,
        private readonly ArkipelaParameterService $arkipelaParameterService,
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
            'parameterOptions' => $this->parameterOptions(),
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
        $this->ensureMandatoryDocuments($order);

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $order,
            action: 'created',
            newValue: $order->load(['client', 'items', 'documents'])->toArray(),
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
            'pageSubtitle' => 'Pantau header order, item sourcing, margin transaksi, dan kesiapan dokumen mandatory.',
            'order' => $order->load(['client', 'creator', 'items.supplier', 'documents.generator', 'documents.verifier']),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'documentStatusBadgeMap' => $this->documentStatusBadgeMap(),
            'documentStatusLabelMap' => $this->documentStatusLabelMap(),
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
                    'item_code' => $item->item_code,
                    'product_name' => $item->product_name,
                    'hs_code' => $item->hs_code,
                    'specification' => $item->specification,
                    'quantity_kg' => $item->quantity_kg,
                    'quantity_pcs' => $item->quantity_pcs,
                    'quantity_unit' => $item->quantity_unit,
                    'pieces_per_package' => $item->pieces_per_package,
                    'package_count' => $item->package_count,
                    'package_type' => $item->package_type,
                    'outer_package_type' => $item->outer_package_type,
                    'length_cm' => $item->length_cm,
                    'width_cm' => $item->width_cm,
                    'height_cm' => $item->height_cm,
                    'dimension_unit' => $item->dimension_unit,
                    'net_weight_kg' => $item->net_weight_kg,
                    'gross_weight_kg' => $item->gross_weight_kg,
                    'package_notes' => $item->package_notes,
                    'selling_price' => $item->selling_price,
                    'buying_price' => $item->buying_price,
                ])
                ->values()
                ->all() ?: [$this->emptyItem()],
            'parameterOptions' => $this->parameterOptions(),
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
        $this->ensureMandatoryDocuments($order);

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $order,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $order->fresh()->load(['client', 'items', 'documents'])->toArray(),
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

    public function generateDocument(Request $request, Order $order, Document $document): RedirectResponse
    {
        $this->ensureDocumentBelongsToOrder($order, $document);

        $oldValue = $document->toArray();
        $generatedDocument = $this->orderDocumentService->generate($document, $order, (int) $request->user()->id);

        $this->activityLogService->log(
            moduleName: 'orders',
            record: $generatedDocument,
            action: $oldValue['generated_at'] ? 'regenerated' : 'generated',
            oldValue: $oldValue,
            newValue: $generatedDocument->toArray(),
            description: "{$generatedDocument->document_number} generated for order {$order->order_code}",
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('status', "{$generatedDocument->document_number} generated successfully.");
    }

    public function previewDocument(Order $order, Document $document): View|RedirectResponse
    {
        $this->ensureDocumentBelongsToOrder($order, $document);

        if (empty($document->snapshot_payload)) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Document has not been generated yet.');
        }

        $documentType = OrderDocumentType::from($document->document_type);

        return view('orders.documents.preview', [
            'pageTitle' => $documentType->label(),
            'pageSubtitle' => "Generated preview untuk {$order->order_code}.",
            'order' => $order->loadMissing(['client', 'items.supplier']),
            'document' => $document->loadMissing(['generator', 'verifier']),
            'documentType' => $documentType,
            'documentPayload' => $document->snapshot_payload,
        ]);
    }

    private function availableSuppliers()
    {
        return Supplier::query()
            ->with('products')
            ->where('approval_status', SupplierApprovalStatus::APPROVED->value)
            ->orderBy('supplier_name')
            ->get(['id', 'supplier_name', 'supplier_code', 'approval_status', 'status', 'products_summary', 'monthly_capacity_kg', 'minimum_order_kg']);
    }

    private function ensureMandatoryDocuments(Order $order): void
    {
        foreach (OrderDocumentType::mandatory() as $type) {
            $order->documents()->firstOrCreate(
                ['document_type' => $type->value],
                ['status' => OrderDocumentStatus::DRAFT->value],
            );
        }
    }

    private function ensureDocumentBelongsToOrder(Order $order, Document $document): void
    {
        abort_unless($document->order_id === $order->id, 404);
    }

    private function emptyItem(): array
    {
        return [
            'supplier_id' => null,
            'item_code' => '',
            'product_name' => '',
            'hs_code' => '',
            'specification' => '',
            'quantity_kg' => null,
            'quantity_pcs' => null,
            'quantity_unit' => 'PCS',
            'pieces_per_package' => null,
            'package_count' => null,
            'package_type' => '',
            'outer_package_type' => '',
            'length_cm' => null,
            'width_cm' => null,
            'height_cm' => null,
            'dimension_unit' => 'CM',
            'net_weight_kg' => null,
            'gross_weight_kg' => null,
            'package_notes' => '',
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
                    'item_code' => $this->normalizeNullableString($item['item_code'] ?? null),
                    'product_name' => $item['product_name'],
                    'hs_code' => $this->normalizeNullableString($item['hs_code'] ?? null),
                    'specification' => $item['specification'] ?: null,
                    'quantity_kg' => round($quantity, 2),
                    'quantity_pcs' => $this->normalizeNullableInteger($item['quantity_pcs'] ?? null),
                    'quantity_unit' => $this->normalizeNullableString($item['quantity_unit'] ?? null) ?: 'PCS',
                    'pieces_per_package' => $this->normalizeNullableInteger($item['pieces_per_package'] ?? null),
                    'package_count' => $this->normalizeNullableInteger($item['package_count'] ?? null),
                    'package_type' => $this->normalizeNullableString($item['package_type'] ?? null),
                    'outer_package_type' => $this->normalizeNullableString($item['outer_package_type'] ?? null),
                    'length_cm' => $this->normalizeNullableDecimal($item['length_cm'] ?? null),
                    'width_cm' => $this->normalizeNullableDecimal($item['width_cm'] ?? null),
                    'height_cm' => $this->normalizeNullableDecimal($item['height_cm'] ?? null),
                    'dimension_unit' => $this->normalizeNullableString($item['dimension_unit'] ?? null) ?: 'CM',
                    'net_weight_kg' => $this->normalizeNullableDecimal($item['net_weight_kg'] ?? null),
                    'gross_weight_kg' => $this->normalizeNullableDecimal($item['gross_weight_kg'] ?? null),
                    'package_notes' => $this->normalizeNullableString($item['package_notes'] ?? null),
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

    private function normalizeNullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeNullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeNullableDecimal(mixed $value, int $precision = 2): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, $precision);
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

    private function parameterOptions(): array
    {
        return [
            'quantity_units' => $this->arkipelaParameterService->options(ArkipelaParameterService::GROUP_QUANTITY_UNIT),
            'dimension_units' => $this->arkipelaParameterService->options(ArkipelaParameterService::GROUP_DIMENSION_UNIT),
            'packaging_types' => $this->arkipelaParameterService->options(ArkipelaParameterService::GROUP_PACKAGING_TYPE),
            'outer_packaging_types' => $this->arkipelaParameterService->options(ArkipelaParameterService::GROUP_OUTER_PACKAGING_TYPE),
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

    private function documentStatusBadgeMap(): array
    {
        return [
            OrderDocumentStatus::DRAFT->value => 'bg-light-secondary',
            OrderDocumentStatus::GENERATED->value => 'bg-light-primary',
            OrderDocumentStatus::OUTDATED->value => 'bg-light-warning text-dark',
            OrderDocumentStatus::VERIFIED->value => 'bg-light-success',
        ];
    }

    private function documentStatusLabelMap(): array
    {
        return collect(OrderDocumentStatus::options())
            ->pluck('label', 'value')
            ->all();
    }
}
