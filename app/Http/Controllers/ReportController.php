<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('reports.dashboard', $request->only([
            'start_date',
            'end_date',
        ]));
    }

    public function dashboard(Request $request): View|Response
    {
        [$orders, $filters] = $this->filteredOrders($request);
        $summaryCards = $this->summaryCards($orders);
        $statusSummary = $this->statusSummary($orders);
        $topClients = $this->topClients($orders);
        $recentOrders = $this->recentOrders($orders);
        $topProducts = $this->topProducts($orders);

        if ($export = $this->exportFormat($request)) {
            return $this->exportResponse(
                format: $export,
                fileName: 'report-dashboard',
                title: 'Report Dashboard',
                filters: $filters,
                sections: [
                    [
                        'title' => 'Summary',
                        'columns' => ['Metric', 'Value', 'Note'],
                        'rows' => collect($summaryCards)->map(fn (array $card) => [
                            $card['label'],
                            $card['value'],
                            $card['hint'],
                        ])->all(),
                    ],
                    [
                        'title' => 'Order Status Summary',
                        'columns' => ['Status', 'Total Orders', 'Revenue', 'Net Profit'],
                        'rows' => collect($statusSummary)->map(fn (array $status) => [
                            $status['label'],
                            number_format($status['count']),
                            $this->formatMoney($status['revenue']),
                            $this->formatMoney($status['profit']),
                        ])->all(),
                    ],
                    [
                        'title' => 'Top Clients',
                        'columns' => ['Client', 'Orders', 'Revenue', 'Profit'],
                        'rows' => collect($topClients)->map(fn (array $client) => [
                            $client['client'],
                            number_format($client['orders']),
                            $this->formatMoney($client['revenue']),
                            $this->formatMoney($client['profit']),
                        ])->all(),
                    ],
                    [
                        'title' => 'Recent Orders',
                        'columns' => ['Order Code', 'Client', 'Status', 'Order Date', 'Delivery Date', 'Revenue', 'Net Profit'],
                        'rows' => collect($recentOrders)->map(fn (array $order) => [
                            $order['order_code'],
                            $order['client'],
                            $order['status'],
                            $order['order_date'],
                            $order['delivery_date'],
                            $this->formatMoney($order['revenue']),
                            $this->formatMoney($order['profit']),
                        ])->all(),
                    ],
                    [
                        'title' => 'Top Products',
                        'columns' => ['Product', 'Line Items', 'Quantity (kg)'],
                        'rows' => collect($topProducts)->map(fn (array $product) => [
                            $product['product'],
                            number_format($product['lines']),
                            number_format($product['quantity_kg'], 2),
                        ])->all(),
                    ],
                ],
            );
        }

        return view('reports.index', [
            'pageTitle' => 'Report Dashboard',
            'pageSubtitle' => 'Ringkasan performa order, revenue, profit, dan client untuk monitoring cepat.',
            'reportRouteName' => 'reports.dashboard',
            'filters' => $filters,
            'summaryCards' => $summaryCards,
            'statusSummary' => $statusSummary,
            'topClients' => $topClients,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }

    public function orders(Request $request): View|Response
    {
        [$orders, $filters] = $this->filteredOrders($request);

        $rows = $orders->map(function (Order $order): array {
            $totalQtyKg = (float) $order->items->sum('quantity_kg');
            $totalQtyPcs = (int) $order->items->sum(fn ($item) => (int) ($item->quantity_pcs ?? 0));
            $totalPackages = (int) $order->items->sum(fn ($item) => (int) ($item->package_count ?? 0));

            return [
                'order_code' => $order->order_code,
                'po_number' => $order->po_number ?: '-',
                'client' => $order->client?->company_name ?? '-',
                'destination_country' => $order->destination_country ?: '-',
                'destination_port' => $order->destination_port ?: '-',
                'shipment_mode' => $order->shipment_mode ?: '-',
                'status' => str($order->status)->replace('_', ' ')->title()->toString(),
                'order_date' => optional($order->order_date)->format('d M Y') ?? '-',
                'delivery_date' => optional($order->delivery_date)->format('d M Y') ?? '-',
                'suppliers' => $this->supplierSummary($order),
                'products' => $this->productSummary($order),
                'qty_kg' => $totalQtyKg,
                'qty_pcs' => $totalQtyPcs,
                'packages' => $totalPackages,
                'revenue' => (float) $order->subtotal_sales,
                'buying' => (float) $order->subtotal_buying,
                'gross_profit' => (float) $order->gross_profit,
                'net_profit' => (float) $order->net_profit,
                'gross_margin' => (float) $order->gross_margin,
                'net_margin' => (float) $order->net_margin,
                'payment_term' => $order->payment_term ?: '-',
                'incoterm' => $order->incoterm ?: '-',
                'document_status' => $this->documentStatusSummary($order),
                'risk_status' => $this->reportOrderRiskStatus($order),
                'notes' => $order->notes ?: '-',
            ];
        });

        if ($export = $this->exportFormat($request)) {
            return $this->exportResponse(
                format: $export,
                fileName: 'report-orders',
                title: 'Order Report',
                filters: $filters,
                sections: [[
                    'title' => 'Orders',
                    'columns' => [
                        'Order Code',
                        'PO Number',
                        'Client',
                        'Destination Country',
                        'Destination Port',
                        'Shipment Mode',
                        'Status',
                        'Order Date',
                        'Delivery Date',
                        'Suppliers',
                        'Products',
                        'Qty KG',
                        'Qty PCS',
                        'Package Count',
                        'Revenue',
                        'Buying',
                        'Gross Profit',
                        'Net Profit',
                        'Gross Margin',
                        'Net Margin',
                        'Payment Term',
                        'Incoterm',
                        'Document Status',
                        'Risk',
                        'Notes',
                    ],
                    'rows' => $rows->map(fn (array $row) => [
                        $row['order_code'],
                        $row['po_number'],
                        $row['client'],
                        $row['destination_country'],
                        $row['destination_port'],
                        $row['shipment_mode'],
                        $row['status'],
                        $row['order_date'],
                        $row['delivery_date'],
                        $row['suppliers'],
                        $row['products'],
                        number_format($row['qty_kg'], 2),
                        number_format($row['qty_pcs']),
                        number_format($row['packages']),
                        $this->formatMoney($row['revenue']),
                        $this->formatMoney($row['buying']),
                        $this->formatMoney($row['gross_profit']),
                        $this->formatMoney($row['net_profit']),
                        number_format($row['gross_margin'], 2) . '%',
                        number_format($row['net_margin'], 2) . '%',
                        $row['payment_term'],
                        $row['incoterm'],
                        $row['document_status'],
                        $row['risk_status'],
                        $row['notes'],
                    ])->all(),
                ]],
            );
        }

        return view('reports.orders', [
            'pageTitle' => 'Order Report',
            'pageSubtitle' => 'Report internal owner dan team untuk monitoring order, supplier, cost, margin, packaging, dan status dokumen.',
            'reportRouteName' => 'reports.orders',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function clients(Request $request): View|Response
    {
        [$orders, $filters] = $this->filteredOrders($request);

        $rows = $this->topClients($orders, limit: null)
            ->map(function (array $client): array {
                $averageRevenue = $client['orders'] > 0 ? $client['revenue'] / $client['orders'] : 0;

                return $client + [
                    'average_revenue' => $averageRevenue,
                ];
            })
            ->values();

        if ($export = $this->exportFormat($request)) {
            return $this->exportResponse(
                format: $export,
                fileName: 'report-clients',
                title: 'Client Report',
                filters: $filters,
                sections: [[
                    'title' => 'Clients',
                    'columns' => ['Client', 'Orders', 'Revenue', 'Profit', 'Average Revenue / Order'],
                    'rows' => $rows->map(fn (array $row) => [
                        $row['client'],
                        number_format($row['orders']),
                        $this->formatMoney($row['revenue']),
                        $this->formatMoney($row['profit']),
                        $this->formatMoney($row['average_revenue']),
                    ])->all(),
                ]],
            );
        }

        return view('reports.clients', [
            'pageTitle' => 'Client Report',
            'pageSubtitle' => 'Performa client berdasarkan jumlah order, revenue, dan profit dalam periode terpilih.',
            'reportRouteName' => 'reports.clients',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    public function products(Request $request): View|Response
    {
        [$orders, $filters] = $this->filteredOrders($request);

        $rows = $this->topProducts($orders, limit: null)
            ->map(function (array $product) use ($orders): array {
                $relatedItems = $orders
                    ->flatMap(fn (Order $order) => $order->items)
                    ->filter(fn ($item) => $item->product_name === $product['product']);

                return $product + [
                    'sales' => (float) $relatedItems->sum('line_total_sales'),
                    'buying' => (float) $relatedItems->sum('line_total_buying'),
                    'profit' => (float) $relatedItems->sum('line_profit'),
                ];
            })
            ->values();

        if ($export = $this->exportFormat($request)) {
            return $this->exportResponse(
                format: $export,
                fileName: 'report-products',
                title: 'Product Report',
                filters: $filters,
                sections: [[
                    'title' => 'Products',
                    'columns' => ['Product', 'Line Items', 'Quantity (kg)', 'Sales', 'Buying', 'Profit'],
                    'rows' => $rows->map(fn (array $row) => [
                        $row['product'],
                        number_format($row['lines']),
                        number_format($row['quantity_kg'], 2),
                        $this->formatMoney($row['sales']),
                        $this->formatMoney($row['buying']),
                        $this->formatMoney($row['profit']),
                    ])->all(),
                ]],
            );
        }

        return view('reports.products', [
            'pageTitle' => 'Product Report',
            'pageSubtitle' => 'Ringkasan performa produk berdasarkan quantity, sales, buying, dan profit.',
            'reportRouteName' => 'reports.products',
            'filters' => $filters,
            'rows' => $rows,
        ]);
    }

    private function filteredOrders(Request $request): array
    {
        $minOrderDate = Order::query()->min('order_date');
        $maxOrderDate = Order::query()->max('order_date');

        $fallbackStart = $minOrderDate
            ? Carbon::parse($minOrderDate)
            : now()->startOfMonth();
        $fallbackEnd = $maxOrderDate
            ? Carbon::parse($maxOrderDate)
            : now();

        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->string('start_date')->toString())
            : $fallbackStart->copy();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->string('end_date')->toString())
            : $fallbackEnd->copy();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $orders = Order::query()
            ->with([
                'client:id,company_name',
                'items:id,order_id,supplier_id,product_name,quantity_kg,quantity_pcs,package_count,line_total_sales,line_total_buying,line_profit',
                'items.supplier:id,supplier_name',
                'documents:id,order_id,document_type,status',
            ])
            ->whereDate('order_date', '>=', $startDate->toDateString())
            ->whereDate('order_date', '<=', $endDate->toDateString())
            ->orderByDesc('order_date')
            ->get();

        return [
            $orders,
            [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'period_label' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
            ],
        ];
    }

    private function summaryCards(Collection $orders): array
    {
        $totalRevenue = (float) $orders->sum('subtotal_sales');
        $totalNetProfit = (float) $orders->sum('net_profit');
        $averageMargin = $totalRevenue > 0 ? ($totalNetProfit / $totalRevenue) * 100 : 0.0;

        $activeStatuses = [
            OrderStatus::DRAFT->value,
            OrderStatus::QUOTATION->value,
            OrderStatus::CONFIRMED->value,
            OrderStatus::PRODUCTION->value,
            OrderStatus::READY_TO_SHIP->value,
            OrderStatus::SHIPPED->value,
        ];

        return [
            [
                'label' => 'Total Orders',
                'value' => number_format($orders->count()),
                'hint' => number_format($orders->whereIn('status', $activeStatuses)->count()) . ' active pipeline',
                'icon' => 'bi-journal-check',
                'color' => 'blue',
            ],
            [
                'label' => 'Revenue',
                'value' => $this->formatMoney($totalRevenue),
                'hint' => 'All order sales value',
                'icon' => 'bi-cash-stack',
                'color' => 'green',
            ],
            [
                'label' => 'Net Profit',
                'value' => $this->formatMoney($totalNetProfit),
                'hint' => 'After additional costs',
                'icon' => 'bi-graph-up-arrow',
                'color' => 'purple',
            ],
            [
                'label' => 'Net Margin',
                'value' => number_format($averageMargin, 2) . '%',
                'hint' => number_format(Client::query()->count()) . ' clients / ' . number_format(Supplier::query()->count()) . ' suppliers',
                'icon' => 'bi-percent',
                'color' => 'red',
            ],
        ];
    }

    private function statusSummary(Collection $orders): Collection
    {
        return collect(OrderStatus::cases())
            ->map(function (OrderStatus $status) use ($orders): array {
                $matching = $orders->where('status', $status->value);

                return [
                    'label' => $status->label(),
                    'count' => $matching->count(),
                    'revenue' => (float) $matching->sum('subtotal_sales'),
                    'profit' => (float) $matching->sum('net_profit'),
                ];
            })
            ->values();
    }

    private function topClients(Collection $orders, ?int $limit = 5): Collection
    {
        $rows = $orders
            ->groupBy(fn (Order $order) => $order->client?->company_name ?? 'Unknown Client')
            ->map(function (Collection $clientOrders, string $clientName): array {
                return [
                    'client' => $clientName,
                    'orders' => $clientOrders->count(),
                    'revenue' => (float) $clientOrders->sum('subtotal_sales'),
                    'profit' => (float) $clientOrders->sum('net_profit'),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return $limit ? $rows->take($limit)->values() : $rows;
    }

    private function recentOrders(Collection $orders): Collection
    {
        return $orders
            ->take(6)
            ->map(function (Order $order): array {
                return [
                    'order_code' => $order->order_code,
                    'client' => $order->client?->company_name ?? '-',
                    'status' => str($order->status)->replace('_', ' ')->title()->toString(),
                    'order_date' => optional($order->order_date)->format('d M Y') ?? '-',
                    'delivery_date' => optional($order->delivery_date)->format('d M Y') ?? '-',
                    'revenue' => (float) $order->subtotal_sales,
                    'profit' => (float) $order->net_profit,
                ];
            })
            ->values();
    }

    private function topProducts(Collection $orders, ?int $limit = 5): Collection
    {
        $rows = $orders
            ->flatMap(fn (Order $order) => $order->items)
            ->groupBy('product_name')
            ->map(function (Collection $items, string $productName): array {
                return [
                    'product' => $productName,
                    'lines' => $items->count(),
                    'quantity_kg' => (float) $items->sum('quantity_kg'),
                ];
            })
            ->sortByDesc('quantity_kg')
            ->values();

        return $limit ? $rows->take($limit)->values() : $rows;
    }

    private function exportFormat(Request $request): ?string
    {
        $format = strtolower($request->string('export')->toString());

        return in_array($format, ['excel', 'html'], true) ? $format : null;
    }

    private function exportResponse(string $format, string $fileName, string $title, array $filters, array $sections): Response
    {
        $extension = $format === 'excel' ? 'xls' : 'html';
        $contentType = $format === 'excel'
            ? 'application/vnd.ms-excel; charset=UTF-8'
            : 'text/html; charset=UTF-8';

        return response()
            ->view('reports.export', [
                'title' => $title,
                'periodLabel' => $filters['period_label'],
                'sections' => $sections,
            ])
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '.' . $extension . '"');
    }

    private function formatMoney(float $value): string
    {
        return 'USD ' . number_format($value, 2);
    }

    private function supplierSummary(Order $order): string
    {
        $suppliers = $order->items
            ->map(fn ($item) => $item->supplier?->supplier_name)
            ->filter()
            ->unique()
            ->values();

        if ($suppliers->isEmpty()) {
            return '-';
        }

        $visible = $suppliers->take(2)->implode(', ');
        $remaining = max($suppliers->count() - 2, 0);

        return $visible . ($remaining > 0 ? ' +' . $remaining . ' more' : '');
    }

    private function productSummary(Order $order): string
    {
        $products = $order->items
            ->pluck('product_name')
            ->filter()
            ->unique()
            ->values();

        if ($products->isEmpty()) {
            return '-';
        }

        $visible = $products->take(2)->implode(', ');
        $remaining = max($products->count() - 2, 0);

        return $visible . ($remaining > 0 ? ' +' . $remaining . ' more' : '');
    }

    private function documentStatusSummary(Order $order): string
    {
        if ($order->documents->isEmpty()) {
            return 'Missing';
        }

        if ($order->documents->contains(fn ($document) => in_array($document->status, [
            'draft',
            'outdated',
        ], true))) {
            return 'Needs Review';
        }

        if ($order->documents->every(fn ($document) => $document->status === 'verified')) {
            return 'Verified';
        }

        return 'Generated';
    }

    private function reportOrderRiskStatus(Order $order): string
    {
        if ($order->delivery_date && $order->delivery_date->isPast() && ! in_array($order->status, [
            OrderStatus::COMPLETED->value,
            OrderStatus::CANCELLED->value,
            OrderStatus::SHIPPED->value,
        ], true)) {
            return 'Delayed Shipment';
        }

        if ((float) $order->net_margin < 10) {
            return 'Low Margin';
        }

        if ($this->documentStatusSummary($order) !== 'Verified') {
            return 'Docs Incomplete';
        }

        return 'On Track';
    }
}
