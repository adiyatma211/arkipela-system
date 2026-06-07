<?php

namespace App\Http\Controllers;

use App\Enums\SupplierApprovalStatus;
use App\Enums\SupplierPhotoType;
use App\Enums\SupplierStatus;
use App\Enums\SupplierType;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'approval_status' => $request->string('approval_status')->toString(),
        ];

        $suppliers = Supplier::query()
            ->with(['creator', 'products', 'submitter', 'approver'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('supplier_code', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%")
                        ->orWhere('pic_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%")
                        ->orWhereHas('products', fn ($productQuery) => $productQuery->where('product_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['supplier_type'], fn ($query, $type) => $query->where('supplier_type', $type))
            ->when($filters['approval_status'], fn ($query, $approvalStatus) => $query->where('approval_status', $approvalStatus))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', [
            'pageTitle' => 'Supplier Management',
            'pageSubtitle' => 'Kelola supplier sourcing Archipela dengan filter, status, approval owner, dan detail operasional dasar.',
            'suppliers' => $suppliers,
            'filters' => $filters,
            'statusOptions' => SupplierStatus::options(),
            'approvalStatusOptions' => SupplierApprovalStatus::options(),
            'typeOptions' => SupplierType::options(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'approvalBadgeMap' => $this->approvalBadgeMap(),
            'approvalLabelMap' => $this->approvalLabelMap(),
            'typeLabelMap' => $this->typeLabelMap(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('suppliers.create', [
            'pageTitle' => 'Add Supplier',
            'pageSubtitle' => 'Input supplier baru untuk procurement dan kirim ke owner untuk approval master data.',
            'supplier' => new Supplier([
                'country' => 'Indonesia',
                'status' => SupplierStatus::PROSPECT->value,
                'approval_status' => SupplierApprovalStatus::PENDING->value,
            ]),
            'productRows' => $this->emptyProductRows(),
            'photoOptions' => SupplierPhotoType::options(),
            'statusOptions' => SupplierStatus::options(),
            'statusLabelMap' => $this->statusLabelMap(),
            'approvalBadgeMap' => $this->approvalBadgeMap(),
            'approvalLabelMap' => $this->approvalLabelMap(),
            'typeOptions' => SupplierType::options(),
            'formAction' => route('suppliers.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Supplier',
            'canManageOperationalStatus' => $request->user()?->isOwner() ?? false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request): RedirectResponse
    {
        $actor = $request->user();
        $payload = $request->validated();
        $productRows = $payload['products'];
        $photoRows = $payload['photos'] ?? [];
        unset($payload['products'], $payload['photos']);

        $payload['supplier_code'] = $this->codeGeneratorService->generateSupplierCode();
        $payload['created_by'] = $actor?->id;
        $payload['status'] = $actor?->isOwner()
            ? $payload['status']
            : SupplierStatus::PROSPECT->value;
        $payload = array_merge(
            $payload,
            $this->buildSupplierProductSummary($productRows),
            $this->buildApprovalPayloadForCreate($actor),
        );

        $storedPaths = [];

        try {
            $supplier = DB::transaction(function () use ($payload, $productRows, $photoRows, $actor, &$storedPaths) {
                $supplier = Supplier::query()->create($payload);
                $this->syncSupplierProducts($supplier, $productRows);
                $this->storeSupplierPhotos($supplier, $photoRows, $actor?->id, $storedPaths);

                return $supplier->load(['products', 'photos', 'submitter', 'approver', 'rejector']);
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('supplier-photos')->delete($storedPaths);
            }

            throw $exception;
        }

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'created',
            newValue: $supplier->fresh(['products', 'photos', 'submitter', 'approver', 'rejector'])?->toArray(),
            description: "Supplier {$supplier->supplier_code} created",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', $actor?->isOwner()
                ? "Supplier {$supplier->supplier_code} created and approved successfully."
                : "Supplier {$supplier->supplier_code} created and submitted for owner approval.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Supplier $supplier): View
    {
        $user = $request->user();

        return view('suppliers.show', [
            'pageTitle' => 'Supplier Detail',
            'pageSubtitle' => 'Ringkasan profil supplier, approval owner, dan kelayakan awal untuk sourcing.',
            'supplier' => $supplier->load(['creator', 'products', 'photos', 'submitter', 'approver', 'rejector']),
            'photoTypeLabelMap' => $this->photoTypeLabelMap(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'approvalBadgeMap' => $this->approvalBadgeMap(),
            'approvalLabelMap' => $this->approvalLabelMap(),
            'typeLabelMap' => $this->typeLabelMap(),
            'canManageOperationalStatus' => $user?->isOwner() ?? false,
            'canApproveSupplier' => $user?->isOwner() ?? false,
            'canSubmitSupplier' => $user && ! $user->isOwner() && $user->hasPermission(\App\Enums\UserPermission::SUPPLIERS_MANAGE->value),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'pageTitle' => 'Edit Supplier',
            'pageSubtitle' => "Update data supplier {$supplier->supplier_code}.",
            'supplier' => $supplier->load(['products', 'photos', 'submitter', 'approver', 'rejector']),
            'productRows' => $this->productRowsForForm($supplier->load('products')),
            'photoOptions' => SupplierPhotoType::options(),
            'photoTypeLabelMap' => $this->photoTypeLabelMap(),
            'statusOptions' => SupplierStatus::options(),
            'statusLabelMap' => $this->statusLabelMap(),
            'approvalBadgeMap' => $this->approvalBadgeMap(),
            'approvalLabelMap' => $this->approvalLabelMap(),
            'typeOptions' => SupplierType::options(),
            'formAction' => route('suppliers.update', $supplier),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Supplier',
            'canManageOperationalStatus' => $request->user()?->isOwner() ?? false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $actor = $request->user();
        $oldValue = $supplier->load(['products', 'photos', 'submitter', 'approver', 'rejector'])->toArray();
        $payload = $request->validated();
        $productRows = $payload['products'];
        $photoRows = $payload['photos'] ?? [];
        $existingPhotosToDelete = $payload['existing_photos_to_delete'] ?? [];
        unset($payload['products'], $payload['photos'], $payload['existing_photos_to_delete']);

        $shouldResetApproval = $this->shouldResetApproval($supplier, $payload, $productRows, $photoRows, $existingPhotosToDelete);

        $payload['status'] = $actor?->isOwner()
            ? $payload['status']
            : $supplier->status;
        $payload = array_merge($payload, $this->buildSupplierProductSummary($productRows));

        if (! $actor?->isOwner() && ($supplier->approval_status !== SupplierApprovalStatus::APPROVED->value || $shouldResetApproval)) {
            $payload = array_merge($payload, $this->buildApprovalPendingPayload($actor));
        }

        $storedPaths = [];
        $pathsToDelete = [];

        try {
            DB::transaction(function () use ($supplier, $payload, $productRows, $photoRows, $existingPhotosToDelete, $actor, &$storedPaths, &$pathsToDelete) {
                $supplier->update($payload);
                $this->syncSupplierProducts($supplier, $productRows);
                $pathsToDelete = $this->deleteSupplierPhotos($supplier, $existingPhotosToDelete);
                $this->storeSupplierPhotos($supplier, $photoRows, $actor?->id, $storedPaths);
            });
        } catch (\Throwable $exception) {
            if ($storedPaths !== []) {
                Storage::disk('supplier-photos')->delete($storedPaths);
            }

            throw $exception;
        }

        if ($pathsToDelete !== []) {
            Storage::disk('supplier-photos')->delete($pathsToDelete);
        }

        $supplier->refresh()->load(['products', 'photos', 'submitter', 'approver', 'rejector']);

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $supplier->toArray(),
            description: "Supplier {$supplier->supplier_code} updated",
        );

        $statusMessage = "Supplier {$supplier->supplier_code} updated successfully.";

        if (! $actor?->isOwner() && $supplier->approval_status === SupplierApprovalStatus::PENDING->value) {
            $statusMessage = $shouldResetApproval || ($oldValue['approval_status'] ?? null) !== SupplierApprovalStatus::PENDING->value
                ? "Supplier {$supplier->supplier_code} updated and submitted for owner approval."
                : "Supplier {$supplier->supplier_code} updated while still waiting for owner approval.";
        }

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', $statusMessage);
    }

    public function submitForApproval(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->isOwner()) {
            abort(403);
        }

        $oldValue = $supplier->load(['submitter', 'approver', 'rejector'])->toArray();

        $supplier->update($this->buildApprovalPendingPayload($user));
        $supplier->refresh()->load(['submitter', 'approver', 'rejector']);

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'submitted_for_approval',
            oldValue: $oldValue,
            newValue: $supplier->toArray(),
            description: "Supplier {$supplier->supplier_code} submitted for owner approval",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Supplier {$supplier->supplier_code} submitted for owner approval.");
    }

    public function approve(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = $this->ensureOwner($request->user());
        $oldValue = $supplier->load(['submitter', 'approver', 'rejector'])->toArray();

        $supplier->update($this->buildApprovalApprovedPayload($user));
        $supplier->refresh()->load(['submitter', 'approver', 'rejector']);

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'approved',
            oldValue: $oldValue,
            newValue: $supplier->toArray(),
            description: "Supplier {$supplier->supplier_code} approved by owner",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Supplier {$supplier->supplier_code} approved successfully.");
    }

    public function reject(Request $request, Supplier $supplier): RedirectResponse
    {
        $user = $this->ensureOwner($request->user());
        $payload = $request->validate([
            'rejection_reason' => ['required', 'string'],
        ]);

        $oldValue = $supplier->load(['submitter', 'approver', 'rejector'])->toArray();

        $supplier->update($this->buildApprovalRejectedPayload($user, $payload['rejection_reason']));
        $supplier->refresh()->load(['submitter', 'approver', 'rejector']);

        $this->activityLogService->log(
            moduleName: 'suppliers',
            record: $supplier,
            action: 'rejected',
            oldValue: $oldValue,
            newValue: $supplier->toArray(),
            description: "Supplier {$supplier->supplier_code} rejected by owner",
        );

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('status', "Supplier {$supplier->supplier_code} rejected.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $oldValue = $supplier->load('photos')->toArray();
        $supplierCode = $supplier->supplier_code;
        $photoPaths = $supplier->photos
            ->pluck('file_path')
            ->filter()
            ->values()
            ->all();

        $supplier->delete();

        if ($photoPaths !== []) {
            Storage::disk('supplier-photos')->delete($photoPaths);
        }

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

    private function ensureOwner(?User $user): User
    {
        if (! $user || ! $user->isOwner()) {
            abort(403);
        }

        return $user;
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

    private function approvalBadgeMap(): array
    {
        return [
            SupplierApprovalStatus::PENDING->value => 'bg-warning text-dark',
            SupplierApprovalStatus::APPROVED->value => 'bg-success',
            SupplierApprovalStatus::REJECTED->value => 'bg-danger',
        ];
    }

    private function statusLabelMap(): array
    {
        return collect(SupplierStatus::options())
            ->pluck('label', 'value')
            ->all();
    }

    private function approvalLabelMap(): array
    {
        return collect(SupplierApprovalStatus::options())
            ->pluck('label', 'value')
            ->all();
    }

    private function typeLabelMap(): array
    {
        return collect(SupplierType::options())
            ->pluck('label', 'value')
            ->all();
    }

    private function photoTypeLabelMap(): array
    {
        return collect(SupplierPhotoType::options())
            ->pluck('label', 'value')
            ->all();
    }

    private function productRowsForForm(Supplier $supplier): array
    {
        $rows = $supplier->resolvedProducts()
            ->map(function (SupplierProduct $product) {
                return [
                    'product_name' => $product->product_name,
                    'monthly_capacity_kg' => $product->monthly_capacity_kg,
                    'minimum_order_kg' => $product->minimum_order_kg,
                ];
            })
            ->values()
            ->all();

        return $rows !== [] ? $rows : $this->emptyProductRows();
    }

    private function emptyProductRows(): array
    {
        return [[
            'product_name' => '',
            'monthly_capacity_kg' => null,
            'minimum_order_kg' => null,
        ]];
    }

    private function syncSupplierProducts(Supplier $supplier, array $productRows): void
    {
        $supplier->products()->delete();

        $supplier->products()->createMany(
            collect($productRows)
                ->values()
                ->map(function (array $productRow, int $index) {
                    return [
                        'product_name' => $productRow['product_name'],
                        'monthly_capacity_kg' => $productRow['monthly_capacity_kg'],
                        'minimum_order_kg' => $productRow['minimum_order_kg'],
                        'sort_order' => $index,
                    ];
                })
                ->all()
        );
    }

    private function deleteSupplierPhotos(Supplier $supplier, array $photoIds): array
    {
        if ($photoIds === []) {
            return [];
        }

        $photos = $supplier->photos()
            ->whereIn('id', $photoIds)
            ->get(['id', 'file_path']);

        if ($photos->isEmpty()) {
            return [];
        }

        $paths = $photos
            ->pluck('file_path')
            ->filter()
            ->values()
            ->all();

        $supplier->photos()->whereIn('id', $photos->pluck('id'))->delete();

        return $paths;
    }

    private function storeSupplierPhotos(Supplier $supplier, array $photoRows, ?int $uploadedBy, array &$storedPaths): void
    {
        if ($photoRows === []) {
            return;
        }

        $sortOrder = (int) $supplier->photos()->count();
        $photoPayloads = [];

        foreach ($photoRows as $photoRow) {
            $file = data_get($photoRow, 'file');

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $fileName = $this->buildSupplierPhotoFileName($supplier, $photoRow['photo_type'], $file);
            $path = $file->storeAs("suppliers/{$supplier->id}/photos", $fileName, 'supplier-photos');
            $storedPaths[] = $path;

            $photoPayloads[] = [
                'photo_type' => $photoRow['photo_type'],
                'file_path' => $path,
                'caption' => $photoRow['caption'] ?: null,
                'sort_order' => $sortOrder++,
                'uploaded_by' => $uploadedBy,
            ];
        }

        if ($photoPayloads !== []) {
            $supplier->photos()->createMany($photoPayloads);
        }
    }

    private function buildApprovalPayloadForCreate(?User $user): array
    {
        if ($user?->isOwner()) {
            return [
                'approval_status' => SupplierApprovalStatus::APPROVED->value,
                'submitted_by' => $user->id,
                'submitted_at' => now(),
                'approved_by' => $user->id,
                'approved_at' => now(),
                'rejected_by' => null,
                'rejected_at' => null,
                'rejection_reason' => null,
            ];
        }

        return $this->buildApprovalPendingPayload($user);
    }

    private function buildApprovalPendingPayload(?User $user): array
    {
        return [
            'approval_status' => SupplierApprovalStatus::PENDING->value,
            'submitted_by' => $user?->id,
            'submitted_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ];
    }

    private function buildApprovalApprovedPayload(User $user): array
    {
        return [
            'approval_status' => SupplierApprovalStatus::APPROVED->value,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
        ];
    }

    private function buildApprovalRejectedPayload(User $user, string $rejectionReason): array
    {
        return [
            'approval_status' => SupplierApprovalStatus::REJECTED->value,
            'approved_by' => null,
            'approved_at' => null,
            'rejected_by' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => trim($rejectionReason),
        ];
    }

    private function shouldResetApproval(
        Supplier $supplier,
        array $payload,
        array $productRows,
        array $photoRows,
        array $existingPhotosToDelete,
    ): bool {
        $fields = [
            'supplier_name',
            'supplier_type',
            'pic_name',
            'phone',
            'email',
            'address',
            'city',
            'province',
            'country',
            'payment_term',
            'legal_status',
        ];

        foreach ($fields as $field) {
            if (($supplier->{$field} ?? null) !== ($payload[$field] ?? null)) {
                return true;
            }
        }

        if ($this->normalizedComparableProducts($supplier) !== $this->normalizedInputProducts($productRows)) {
            return true;
        }

        return $photoRows !== [] || $existingPhotosToDelete !== [];
    }

    private function normalizedComparableProducts(Supplier $supplier): array
    {
        return $supplier->resolvedProducts()
            ->map(fn (SupplierProduct $product) => [
                'product_name' => $product->product_name,
                'monthly_capacity_kg' => $product->monthly_capacity_kg !== null ? (string) $product->monthly_capacity_kg : null,
                'minimum_order_kg' => $product->minimum_order_kg !== null ? (string) $product->minimum_order_kg : null,
            ])
            ->values()
            ->all();
    }

    private function normalizedInputProducts(array $productRows): array
    {
        return collect($productRows)
            ->map(fn (array $productRow) => [
                'product_name' => $productRow['product_name'],
                'monthly_capacity_kg' => $productRow['monthly_capacity_kg'] !== null ? (string) $productRow['monthly_capacity_kg'] : null,
                'minimum_order_kg' => $productRow['minimum_order_kg'] !== null ? (string) $productRow['minimum_order_kg'] : null,
            ])
            ->values()
            ->all();
    }

    private function buildSupplierPhotoFileName(Supplier $supplier, string $photoType, UploadedFile $file): string
    {
        $supplierCode = preg_replace('/[^A-Za-z0-9\-]+/', '-', $supplier->supplier_code) ?: 'supplier';
        $normalizedPhotoType = Str::slug($photoType, '-');
        $normalizedSupplierName = Str::limit(Str::slug($supplier->supplier_name, '-'), 20, '');
        $timestamp = now()->format('ymdHi');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');

        return "{$supplierCode}-{$normalizedPhotoType}-{$normalizedSupplierName}-{$timestamp}.{$extension}";
    }

    private function buildSupplierProductSummary(array $productRows): array
    {
        $rows = collect($productRows);
        $capacities = $rows->pluck('monthly_capacity_kg')->filter(fn ($value) => $value !== null && $value !== '');
        $minimumOrders = $rows->pluck('minimum_order_kg')->filter(fn ($value) => $value !== null && $value !== '');

        return [
            'products_summary' => $rows->pluck('product_name')->filter()->implode(', '),
            'monthly_capacity_kg' => $capacities->isNotEmpty()
                ? $capacities->sum(fn ($value) => (float) $value)
                : null,
            'minimum_order_kg' => $minimumOrders->isNotEmpty()
                ? $minimumOrders->min(fn ($value) => (float) $value)
                : null,
        ];
    }
}
