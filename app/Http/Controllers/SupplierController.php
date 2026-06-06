<?php

namespace App\Http\Controllers;

use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Services\ActivityLogService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
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
            'supplier_type' => $request->string('supplier_type')->toString(),
        ];

        $suppliers = Supplier::query()
            ->with('creator')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('supplier_code', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%")
                        ->orWhere('pic_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['supplier_type'], fn ($query, $type) => $query->where('supplier_type', $type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', [
            'pageTitle' => 'Supplier Management',
            'pageSubtitle' => 'Kelola supplier sourcing Archipela dengan filter, status, dan detail operasional dasar.',
            'suppliers' => $suppliers,
            'filters' => $filters,
            'statusOptions' => SupplierStatus::options(),
            'typeOptions' => SupplierType::options(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'typeLabelMap' => $this->typeLabelMap(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('suppliers.create', [
            'pageTitle' => 'Add Supplier',
            'pageSubtitle' => 'Input supplier baru untuk procurement dan sourcing database.',
            'supplier' => new Supplier([
                'country' => 'Indonesia',
                'status' => SupplierStatus::PROSPECT->value,
            ]),
            'statusOptions' => SupplierStatus::options(),
            'typeOptions' => SupplierType::options(),
            'formAction' => route('suppliers.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Supplier',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['supplier_code'] = $this->codeGeneratorService->generateSupplierCode();
        $payload['created_by'] = $request->user()?->id;

        $supplier = Supplier::query()->create($payload);

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'created',
            newValue: $supplier->fresh()?->toArray(),
            description: "Supplier {$supplier->supplier_code} created",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Supplier {$supplier->supplier_code} created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View
    {
        return view('suppliers.show', [
            'pageTitle' => 'Supplier Detail',
            'pageSubtitle' => 'Ringkasan profil supplier dan kelayakan awal untuk sourcing.',
            'supplier' => $supplier->load('creator'),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'typeLabelMap' => $this->typeLabelMap(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'pageTitle' => 'Edit Supplier',
            'pageSubtitle' => "Update data supplier {$supplier->supplier_code}.",
            'supplier' => $supplier,
            'statusOptions' => SupplierStatus::options(),
            'typeOptions' => SupplierType::options(),
            'formAction' => route('suppliers.update', $supplier),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Supplier',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $oldValue = $supplier->toArray();
        $supplier->update($request->validated());

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $supplier->fresh()?->toArray(),
            description: "Supplier {$supplier->supplier_code} updated",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Supplier {$supplier->supplier_code} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $oldValue = $supplier->toArray();
        $supplierCode = $supplier->supplier_code;
        $supplier->delete();

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'deleted',
            oldValue: $oldValue,
            description: "Supplier {$supplierCode} deleted",
        );

        return redirect()
            ->route('suppliers.index')
            ->with('status', "Supplier {$supplierCode} deleted successfully.");
    }

    private function statusBadgeMap(): array
    {
        return [
            SupplierStatus::PROSPECT->value => 'bg-light-secondary',
            SupplierStatus::CONTACTED->value => 'bg-light-info',
            SupplierStatus::SAMPLE_REQUESTED->value => 'bg-light-warning',
            SupplierStatus::SAMPLE_RECEIVED->value => 'bg-light-primary',
            SupplierStatus::QC_CHECKING->value => 'bg-light-info',
            SupplierStatus::APPROVED->value => 'bg-light-success',
            SupplierStatus::ACTIVE->value => 'bg-success',
            SupplierStatus::HOLD->value => 'bg-warning text-dark',
            SupplierStatus::REJECTED->value => 'bg-danger',
            SupplierStatus::BLACKLISTED->value => 'bg-dark',
        ];
    }

    private function statusLabelMap(): array
    {
        return collect(SupplierStatus::options())
            ->pluck('label', 'value')
            ->all();
    }

    private function typeLabelMap(): array
    {
        return collect(SupplierType::options())
            ->pluck('label', 'value')
            ->all();
    }
}
