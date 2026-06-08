<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatus;
use App\Enums\OrderDocumentStatus;
use App\Enums\OrderStatus;
use App\Enums\SupplierApprovalStatus;
use App\Enums\SupplierStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $orders = Order::query()
            ->with([
                'client:id,company_name',
                'items:id,order_id,supplier_id,product_name,quantity_kg,buying_price',
                'documents:id,order_id,document_type,status',
            ])
            ->orderByDesc('order_date')
            ->get();

        $clients = Client::query()->get(['id', 'company_name', 'status']);
        $suppliers = Supplier::query()->with('products:id,supplier_id,product_name,monthly_capacity_kg,minimum_order_kg')->get();

        $activeOrderStatuses = [
            OrderStatus::DRAFT->value,
            OrderStatus::QUOTATION->value,
            OrderStatus::CONFIRMED->value,
            OrderStatus::PRODUCTION->value,
            OrderStatus::READY_TO_SHIP->value,
            OrderStatus::SHIPPED->value,
        ];

        $pipelineOrderStatuses = [
            OrderStatus::DRAFT->value,
            OrderStatus::QUOTATION->value,
        ];

        $confirmedRevenueStatuses = [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PRODUCTION->value,
            OrderStatus::READY_TO_SHIP->value,
            OrderStatus::SHIPPED->value,
            OrderStatus::COMPLETED->value,
        ];

        $activeOrders = $orders->whereIn('status', $activeOrderStatuses)->values();
        $pipelineOrders = $orders->whereIn('status', $pipelineOrderStatuses);
        $confirmedRevenueOrders = $orders->whereIn('status', $confirmedRevenueStatuses);
        $nonCancelledOrders = $orders->where('status', '!=', OrderStatus::CANCELLED->value)->values();

        $approvedSupplierStatuses = [
            SupplierStatus::APPROVED->value,
            SupplierStatus::ACTIVE->value,
        ];
        $pendingQcSupplierStatuses = [
            SupplierStatus::SAMPLE_REQUESTED->value,
            SupplierStatus::SAMPLE_RECEIVED->value,
            SupplierStatus::QC_CHECKING->value,
        ];
        $rejectedSupplierStatuses = [
            SupplierStatus::REJECTED->value,
            SupplierStatus::BLACKLISTED->value,
        ];

        $approvedSuppliers = $suppliers->filter(function (Supplier $supplier) use ($approvedSupplierStatuses) {
            return in_array($supplier->status, $approvedSupplierStatuses, true)
                || $supplier->approval_status === SupplierApprovalStatus::APPROVED->value;
        })->values();
        $pendingQcSuppliers = $suppliers->whereIn('status', $pendingQcSupplierStatuses)->values();
        $rejectedQcSuppliers = $suppliers->filter(function (Supplier $supplier) use ($rejectedSupplierStatuses) {
            return in_array($supplier->status, $rejectedSupplierStatuses, true)
                || $supplier->approval_status === SupplierApprovalStatus::REJECTED->value;
        })->values();

        $qcPassRate = $suppliers->count() > 0
            ? ($approvedSuppliers->count() / $suppliers->count()) * 100
            : 0;

        $pendingPaymentOrders = $orders->whereIn('status', [
            OrderStatus::SHIPPED->value,
            OrderStatus::READY_TO_SHIP->value,
        ]);
        $delayedShipmentOrders = $orders->filter(function (Order $order) {
            if (! $order->delivery_date) {
                return false;
            }

            return $order->delivery_date->isPast()
                && ! in_array($order->status, [
                    OrderStatus::COMPLETED->value,
                    OrderStatus::CANCELLED->value,
                    OrderStatus::SHIPPED->value,
                ], true);
        })->values();

        $summaryCards = [
            [
                'label' => 'Total Active Orders',
                'value' => number_format($activeOrders->count()),
                'icon' => 'bi-box-seam',
                'color' => 'blue',
                'hint' => 'Order berjalan',
            ],
            [
                'label' => 'Total Revenue Pipeline',
                'value' => $this->formatMoney((float) $pipelineOrders->sum('subtotal_sales')),
                'icon' => 'bi-graph-up-arrow',
                'color' => 'purple',
                'hint' => 'Draft + quotation orders',
            ],
            [
                'label' => 'Total Confirmed Revenue',
                'value' => $this->formatMoney((float) $confirmedRevenueOrders->sum('subtotal_sales')),
                'icon' => 'bi-cash-stack',
                'color' => 'green',
                'hint' => 'Confirmed to completed',
            ],
            [
                'label' => 'Estimated Gross Profit',
                'value' => $this->formatMoney((float) $nonCancelledOrders->sum('gross_profit')),
                'icon' => 'bi-piggy-bank',
                'color' => 'red',
                'hint' => 'Revenue - COGS before extra costs',
            ],
            [
                'label' => 'Average Gross Margin',
                'value' => number_format((float) $nonCancelledOrders->avg('gross_margin'), 2) . '%',
                'icon' => 'bi-percent',
                'color' => 'blue',
                'hint' => 'Order average',
            ],
            [
                'label' => 'Total Active Clients',
                'value' => number_format($clients->whereIn('status', [
                    ClientStatus::ACTIVE_BUYER->value,
                    ClientStatus::REPEAT_BUYER->value,
                ])->count()),
                'icon' => 'bi-people-fill',
                'color' => 'purple',
                'hint' => 'Buyer active',
            ],
            [
                'label' => 'Total Active Suppliers',
                'value' => number_format($approvedSuppliers->count()),
                'icon' => 'bi-truck',
                'color' => 'green',
                'hint' => 'Approved and active',
            ],
            [
                'label' => 'QC Pass Rate',
                'value' => number_format($qcPassRate, 2) . '%',
                'icon' => 'bi-shield-check',
                'color' => 'green',
                'hint' => 'Based on supplier approval/QC stage',
            ],
            [
                'label' => 'Pending QC',
                'value' => number_format($pendingQcSuppliers->count()),
                'icon' => 'bi-hourglass-split',
                'color' => 'purple',
                'hint' => 'Need review',
            ],
            [
                'label' => 'Rejected QC',
                'value' => number_format($rejectedQcSuppliers->count()),
                'icon' => 'bi-x-octagon',
                'color' => 'red',
                'hint' => 'Rejected supplier/QC stage',
            ],
            [
                'label' => 'Pending Payment',
                'value' => number_format($pendingPaymentOrders->count()),
                'icon' => 'bi-wallet2',
                'color' => 'blue',
                'hint' => 'Ready to ship / shipped',
            ],
            [
                'label' => 'Delayed Shipment',
                'value' => number_format($delayedShipmentOrders->count()),
                'icon' => 'bi-exclamation-triangle',
                'color' => 'red',
                'hint' => 'Past delivery date',
            ],
        ];

        $salesPipeline = collect([
            ClientStatus::LEAD,
            ClientStatus::CONTACTED,
            ClientStatus::QUALIFIED,
            ClientStatus::QUOTATION_SENT,
            ClientStatus::NEGOTIATION,
            ClientStatus::PO_RECEIVED,
            ClientStatus::ACTIVE_BUYER,
            ClientStatus::LOST,
        ])->map(function (ClientStatus $status) use ($clients): array {
            return [
                'label' => $status->label(),
                'count' => $clients->where('status', $status->value)->count(),
            ];
        })->all();

        $qcSummary = [
            ['label' => 'Passed', 'count' => $approvedSuppliers->count(), 'badge' => 'bg-success'],
            ['label' => 'Hold', 'count' => $suppliers->where('status', SupplierStatus::HOLD->value)->count(), 'badge' => 'bg-warning text-dark'],
            ['label' => 'Rejected', 'count' => $rejectedQcSuppliers->count(), 'badge' => 'bg-danger'],
            ['label' => 'Pending Review', 'count' => $pendingQcSuppliers->count(), 'badge' => 'bg-primary'],
        ];

        $activeOrderSummary = $activeOrders
            ->take(6)
            ->map(function (Order $order): array {
                $productList = $order->items
                    ->pluck('product_name')
                    ->filter()
                    ->unique()
                    ->take(2)
                    ->implode(', ');
                $moreProducts = max($order->items->pluck('product_name')->filter()->unique()->count() - 2, 0);
                $productSummary = $productList !== ''
                    ? $productList . ($moreProducts > 0 ? ' +' . $moreProducts . ' more' : '')
                    : '-';
                $quantityKg = (float) $order->items->sum('quantity_kg');
                $riskStatus = $this->orderRiskStatus($order);

                return [
                    'order_code' => $order->order_code,
                    'client' => $order->client?->company_name ?? '-',
                    'country' => $order->destination_country ?: '-',
                    'product' => $productSummary,
                    'quantity' => number_format($quantityKg, 2) . ' kg',
                    'status' => str($order->status)->replace('_', ' ')->title()->toString(),
                    'target_shipment_date' => optional($order->delivery_date)->format('d M Y') ?? '-',
                    'estimated_revenue' => $this->formatMoney((float) $order->subtotal_sales),
                    'estimated_margin' => number_format((float) $order->gross_margin, 2) . '%',
                    'risk_status' => $riskStatus,
                ];
            })
            ->all();

        $riskAlerts = [
            $this->buildRiskAlert(
                'QC Rejected',
                $rejectedQcSuppliers->count(),
                'supplier rejected / blacklisted'
            ),
            $this->buildRiskAlert(
                'QC Hold > 2 Days',
                $suppliers->where('status', SupplierStatus::HOLD->value)->count(),
                'supplier on hold'
            ),
            $this->buildRiskAlert(
                'Shipment < 7 Days but QC Pending',
                $activeOrders->filter(function (Order $order) use ($pendingQcSuppliers) {
                    if (! $order->delivery_date) {
                        return false;
                    }

                    $daysLeft = now()->diffInDays($order->delivery_date, false);
                    $orderSupplierIds = $order->items->pluck('supplier_id')->filter()->all();

                    return $daysLeft <= 7
                        && $daysLeft >= 0
                        && $pendingQcSuppliers->pluck('id')->intersect($orderSupplierIds)->isNotEmpty();
                })->count(),
                'near shipment with pending supplier QC'
            ),
            $this->buildRiskAlert(
                'Payment Overdue',
                $pendingPaymentOrders->count(),
                'ready to ship / shipped awaiting settlement'
            ),
            $this->buildRiskAlert(
                'Supplier Delay',
                $delayedShipmentOrders->count(),
                'delivery date passed'
            ),
            $this->buildRiskAlert(
                'Order Not Updated > 3 Days',
                $activeOrders->filter(fn (Order $order) => $order->updated_at?->lt(now()->subDays(3)))->count(),
                'active order stale update'
            ),
            $this->buildRiskAlert(
                'Margin Below Threshold',
                $nonCancelledOrders->filter(fn (Order $order) => (float) $order->gross_margin < 10)->count(),
                'gross margin below 10%'
            ),
            $this->buildRiskAlert(
                'Export Documents Incomplete',
                $activeOrders->filter(function (Order $order) {
                    return $order->documents->count() < 2
                        || $order->documents->contains(fn ($document) => in_array($document->status, [
                            OrderDocumentStatus::DRAFT->value,
                            OrderDocumentStatus::OUTDATED->value,
                        ], true));
                })->count(),
                'mandatory documents draft/outdated/missing'
            ),
        ];

        $supplierPerformance = $suppliers
            ->map(function (Supplier $supplier) use ($orders): array {
                $linkedItems = $orders->flatMap->items->where('supplier_id', $supplier->id);
                $linkedOrdersCount = $linkedItems->pluck('order_id')->unique()->count();
                $averagePrice = $linkedItems->count() > 0
                    ? (float) $linkedItems->avg('buying_price')
                    : 0.0;
                $linkedOrders = $orders->filter(fn (Order $order) => $order->items->contains('supplier_id', $supplier->id));
                $delayedCount = $linkedOrders->filter(function (Order $order) {
                    return $order->delivery_date
                        && $order->delivery_date->isPast()
                        && ! in_array($order->status, [
                            OrderStatus::COMPLETED->value,
                            OrderStatus::CANCELLED->value,
                            OrderStatus::SHIPPED->value,
                        ], true);
                })->count();

                return [
                    'supplier' => $supplier->supplier_name,
                    'qc_pass_rate' => $this->supplierQcRate($supplier),
                    'average_price' => $averagePrice > 0 ? $this->formatMoney($averagePrice) : 'USD 0.00',
                    'supply_capacity' => number_format((float) ($supplier->resolvedMonthlyCapacityKg() ?? 0), 2) . ' kg',
                    'delivery_reliability' => $delayedCount === 0 ? 'On Track' : 'Delayed ' . $delayedCount,
                    'response_speed' => str($supplier->approval_status ?? 'pending')->replace('_', ' ')->title()->toString(),
                    'total_orders_supplied' => number_format($linkedOrdersCount),
                ];
            })
            ->sortByDesc(fn (array $supplier) => (int) str_replace(',', '', $supplier['total_orders_supplied']))
            ->take(5)
            ->values()
            ->all();

        return view('dashboard.index', [
            'pageTitle' => 'Dashboard Owner',
            'pageSubtitle' => 'Ringkasan utama operasional export Archipela berdasarkan data order, client, supplier, dan dokumen yang tersedia saat ini.',
            'summaryCards' => $summaryCards,
            'salesPipeline' => $salesPipeline,
            'activeOrders' => $activeOrderSummary,
            'qcSummary' => $qcSummary,
            'supplierPerformance' => $supplierPerformance,
            'riskAlerts' => $riskAlerts,
        ]);
    }

    private function formatMoney(float $value): string
    {
        return 'USD ' . number_format($value, 2);
    }

    private function orderRiskStatus(Order $order): string
    {
        if ($order->delivery_date && $order->delivery_date->isPast() && ! in_array($order->status, [
            OrderStatus::COMPLETED->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::SHIPPED->value,
        ], true)) {
            return 'Delayed';
        }

        if ((float) $order->gross_margin < 10) {
            return 'Low Margin';
        }

        if ($order->documents->count() < 2) {
            return 'Docs Pending';
        }

        return 'On Track';
    }

    private function supplierQcRate(Supplier $supplier): string
    {
        if ($supplier->approval_status === SupplierApprovalStatus::APPROVED->value
            || in_array($supplier->status, [
                SupplierStatus::APPROVED->value,
                SupplierStatus::ACTIVE->value,
            ], true)) {
            return '100%';
        }

        if ($supplier->approval_status === SupplierApprovalStatus::REJECTED->value
            || in_array($supplier->status, [
                SupplierStatus::REJECTED->value,
                SupplierStatus::BLACKLISTED->value,
            ], true)) {
            return '0%';
        }

        if ($supplier->status === SupplierStatus::HOLD->value) {
            return '50%';
        }

        return '25%';
    }

    private function buildRiskAlert(string $title, int $count, string $context): array
    {
        if ($count <= 0) {
            return [
                'title' => $title,
                'level' => 'Medium',
                'status' => 'No active alert yet',
            ];
        }

        $level = $count >= 3 ? 'Critical' : ($count >= 2 ? 'High' : 'Medium');

        return [
            'title' => $title,
            'level' => $level,
            'status' => $count . ' ' . $context,
        ];
    }
}
