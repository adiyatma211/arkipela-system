<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'pageTitle' => 'Dashboard Owner',
            'pageSubtitle' => 'Ringkasan utama operasional export Archipela sesuai struktur MVP owner dashboard.',
            'summaryCards' => [
                ['label' => 'Total Active Orders', 'value' => '0', 'icon' => 'bi-box-seam', 'color' => 'blue', 'hint' => 'Order berjalan'],
                ['label' => 'Total Revenue Pipeline', 'value' => 'Rp 0', 'icon' => 'bi-graph-up-arrow', 'color' => 'purple', 'hint' => 'Lead to quotation'],
                ['label' => 'Total Confirmed Revenue', 'value' => 'Rp 0', 'icon' => 'bi-cash-stack', 'color' => 'green', 'hint' => 'PO confirmed'],
                ['label' => 'Estimated Gross Profit', 'value' => 'Rp 0', 'icon' => 'bi-piggy-bank', 'color' => 'red', 'hint' => 'Revenue - COGS - costs'],
                ['label' => 'Average Gross Margin', 'value' => '0%', 'icon' => 'bi-percent', 'color' => 'blue', 'hint' => 'Order average'],
                ['label' => 'Total Active Clients', 'value' => '0', 'icon' => 'bi-people-fill', 'color' => 'purple', 'hint' => 'Buyer active'],
                ['label' => 'Total Active Suppliers', 'value' => '0', 'icon' => 'bi-truck', 'color' => 'green', 'hint' => 'Approved and active'],
                ['label' => 'QC Pass Rate', 'value' => '0%', 'icon' => 'bi-shield-check', 'color' => 'green', 'hint' => 'Passed batches'],
                ['label' => 'Pending QC', 'value' => '0', 'icon' => 'bi-hourglass-split', 'color' => 'purple', 'hint' => 'Need review'],
                ['label' => 'Rejected QC', 'value' => '0', 'icon' => 'bi-x-octagon', 'color' => 'red', 'hint' => 'Rejected batches'],
                ['label' => 'Pending Payment', 'value' => '0', 'icon' => 'bi-wallet2', 'color' => 'blue', 'hint' => 'Need collection'],
                ['label' => 'Delayed Shipment', 'value' => '0', 'icon' => 'bi-exclamation-triangle', 'color' => 'red', 'hint' => 'Shipment risk'],
            ],
            'salesPipeline' => [
                ['label' => 'Lead', 'count' => 0],
                ['label' => 'Contacted', 'count' => 0],
                ['label' => 'Qualified', 'count' => 0],
                ['label' => 'Quotation Sent', 'count' => 0],
                ['label' => 'Negotiation', 'count' => 0],
                ['label' => 'PO Received', 'count' => 0],
                ['label' => 'Active Buyer', 'count' => 0],
                ['label' => 'Lost', 'count' => 0],
            ],
            'activeOrders' => [],
            'qcSummary' => [
                ['label' => 'Passed', 'count' => 0, 'badge' => 'bg-success'],
                ['label' => 'Hold', 'count' => 0, 'badge' => 'bg-warning text-dark'],
                ['label' => 'Rejected', 'count' => 0, 'badge' => 'bg-danger'],
                ['label' => 'Pending Review', 'count' => 0, 'badge' => 'bg-primary'],
            ],
            'supplierPerformance' => [],
            'riskAlerts' => [
                ['title' => 'QC Rejected', 'level' => 'High', 'status' => 'No active alert yet'],
                ['title' => 'QC Hold > 2 Days', 'level' => 'High', 'status' => 'No active alert yet'],
                ['title' => 'Shipment < 7 Days but QC Pending', 'level' => 'Critical', 'status' => 'No active alert yet'],
                ['title' => 'Payment Overdue', 'level' => 'Critical', 'status' => 'No active alert yet'],
                ['title' => 'Supplier Delay', 'level' => 'Medium', 'status' => 'No active alert yet'],
                ['title' => 'Order Not Updated > 3 Days', 'level' => 'Medium', 'status' => 'No active alert yet'],
                ['title' => 'Margin Below Threshold', 'level' => 'High', 'status' => 'No active alert yet'],
                ['title' => 'Export Documents Incomplete', 'level' => 'High', 'status' => 'No active alert yet'],
            ],
        ]);
    }
}
