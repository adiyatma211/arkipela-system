<?php

namespace App\Http\Controllers;

use App\Enums\BarcodeType;
use App\Enums\UserPermission;
use App\Http\Requests\ProductPackagingRequest;
use App\Models\ProductPackaging;
use App\Models\ProductSku;
use App\Services\ActivityLogService;
use App\Services\BarcodeAssetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class ProductPackagingController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly BarcodeAssetService $barcodeAssetService,
    ) {
    }

    public function index(Request $request, ProductSku $productSku): View
    {
        return view('product-packagings.index', [
            'pageTitle' => 'SKU Packaging',
            'pageSubtitle' => "Kelola level packaging untuk SKU {$productSku->sku_code}.",
            'productSku' => $productSku->load(['product', 'packagings']),
            'canManageProducts' => $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false,
        ]);
    }

    public function create(Request $request, ProductSku $productSku): View
    {
        return view('product-packagings.create', [
            'pageTitle' => 'Add Packaging',
            'pageSubtitle' => "Tambah level packaging untuk SKU {$productSku->sku_code}.",
            'productSku' => $productSku->load('product'),
            'productPackaging' => new ProductPackaging([
                'level' => 'each',
                'dimension_unit' => 'CM',
                'is_default_for_level' => false,
            ]),
            'formAction' => route('product-skus.packagings.store', $productSku),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Packaging',
            'levelOptions' => $this->levelOptions(),
            'barcodeTypeOptions' => BarcodeType::options(),
        ]);
    }

    public function store(ProductPackagingRequest $request, ProductSku $productSku): RedirectResponse
    {
        $payload = $request->validated();
        $payload['product_sku_id'] = $productSku->id;

        $productPackaging = ProductPackaging::query()->create($payload);
        $this->barcodeAssetService->syncProductPackaging($productPackaging);
        $productPackaging->refresh();

        $this->activityLogService->log(
            moduleName: 'product_packagings',
            record: $productPackaging,
            action: 'created',
            newValue: $productPackaging->fresh(['productSku'])?->toArray(),
            description: "Packaging {$productPackaging->level} created for SKU {$productSku->sku_code}",
        );

        return redirect()
            ->route('product-skus.packagings.index', $productSku)
            ->with('status', "Packaging {$productPackaging->level} created successfully.");
    }

    public function edit(ProductPackaging $productPackaging): View
    {
        return view('product-packagings.edit', [
            'pageTitle' => 'Edit Packaging',
            'pageSubtitle' => "Update packaging level {$productPackaging->level}.",
            'productSku' => $productPackaging->productSku()->with('product')->firstOrFail(),
            'productPackaging' => $productPackaging,
            'formAction' => route('product-packagings.update', $productPackaging),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Packaging',
            'levelOptions' => $this->levelOptions(),
            'barcodeTypeOptions' => BarcodeType::options(),
        ]);
    }

    public function downloadBarcode(ProductPackaging $productPackaging, string $format): Response|RedirectResponse
    {
        try {
            $download = $this->barcodeAssetService->buildDownload($productPackaging, $format);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('product-skus.packagings.index', $productPackaging->productSku)
                ->with('error', $exception->getMessage());
        }

        return response($download['contents'], 200, [
            'Content-Type' => $download['mime_type'],
            'Content-Disposition' => 'attachment; filename="' . $download['filename'] . '"',
        ]);
    }

    public function update(ProductPackagingRequest $request, ProductPackaging $productPackaging): RedirectResponse
    {
        $oldValue = $productPackaging->toArray();
        $productPackaging->update($request->validated());
        $this->barcodeAssetService->syncProductPackaging($productPackaging);
        $productPackaging->refresh();

        $this->activityLogService->log(
            moduleName: 'product_packagings',
            record: $productPackaging,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $productPackaging->fresh(['productSku'])?->toArray(),
            description: "Packaging {$productPackaging->level} updated",
        );

        return redirect()
            ->route('product-skus.packagings.index', $productPackaging->productSku)
            ->with('status', "Packaging {$productPackaging->level} updated successfully.");
    }

    public function destroy(ProductPackaging $productPackaging): RedirectResponse
    {
        $oldValue = $productPackaging->toArray();
        $level = $productPackaging->level;
        $productSku = $productPackaging->productSku;
        $this->barcodeAssetService->deleteBarcodeAsset($productPackaging);
        $productPackaging->delete();

        $this->activityLogService->log(
            moduleName: 'product_packagings',
            record: $productPackaging,
            action: 'deleted',
            oldValue: $oldValue,
            description: "Packaging {$level} deleted",
        );

        return redirect()
            ->route('product-skus.packagings.index', $productSku)
            ->with('status', "Packaging {$level} deleted successfully.");
    }

    private function levelOptions(): array
    {
        return [
            ['value' => 'each', 'label' => 'Each'],
            ['value' => 'inner', 'label' => 'Inner'],
            ['value' => 'case', 'label' => 'Case'],
            ['value' => 'pallet', 'label' => 'Pallet'],
        ];
    }
}
