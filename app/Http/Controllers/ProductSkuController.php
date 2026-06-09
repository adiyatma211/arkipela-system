<?php

namespace App\Http\Controllers;

use App\Enums\BarcodeType;
use App\Enums\UserPermission;
use App\Http\Requests\ProductSkuRequest;
use App\Models\Product;
use App\Models\ProductSku;
use App\Services\ActivityLogService;
use App\Services\BarcodeAssetService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSkuController extends Controller
{
    public function __construct(
        private readonly CodeGeneratorService $codeGeneratorService,
        private readonly ActivityLogService $activityLogService,
        private readonly BarcodeAssetService $barcodeAssetService,
    ) {
    }

    public function index(Request $request, Product $product): View
    {
        $canManageProducts = $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false;
        $filters = [
            'search' => $request->string('search')->toString(),
            'retail' => $request->string('retail')->toString(),
            'active' => $request->string('active')->toString(),
        ];

        $skus = $product->skus()
            ->with(['creator', 'packagings'])
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('sku_code', 'like', "%{$search}%")
                        ->orWhere('variant_name', 'like', "%{$search}%")
                        ->orWhere('brand_name', 'like', "%{$search}%")
                        ->orWhere('barcode_number', 'like', "%{$search}%")
                        ->orWhere('gtin', 'like', "%{$search}%");
                });
            })
            ->when($filters['retail'] !== '', fn ($query) => $query->where('is_retail_sellable', $filters['retail'] === '1'))
            ->when($filters['active'] !== '', fn ($query) => $query->where('is_active', $filters['active'] === '1'))
            ->paginate(10)
            ->withQueryString();

        return view('product-skus.index', [
            'pageTitle' => 'Product SKU Master',
            'pageSubtitle' => "Kelola turunan SKU dan barcode retail untuk {$product->product_name}.",
            'product' => $product,
            'skus' => $skus,
            'filters' => $filters,
            'canManageProducts' => $canManageProducts,
        ]);
    }

    public function create(Request $request, Product $product): View
    {
        return view('product-skus.create', [
            'pageTitle' => 'Add Product SKU',
            'pageSubtitle' => "Tambah SKU retail atau varian baru untuk {$product->product_name}.",
            'product' => $product,
            'productSku' => new ProductSku([
                'weight_unit' => 'G',
                'sellable_unit' => 'EACH',
                'is_retail_sellable' => true,
                'is_active' => true,
            ]),
            'formAction' => route('products.skus.store', $product),
            'formMethod' => 'POST',
            'submitLabel' => 'Save SKU',
            'canManageProducts' => $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false,
            'barcodeTypeOptions' => BarcodeType::options(),
        ]);
    }

    public function store(ProductSkuRequest $request, Product $product): RedirectResponse
    {
        $payload = $request->validated();
        $payload['product_id'] = $product->id;
        $payload['sku_code'] = $this->codeGeneratorService->generateProductSkuCode();
        $payload['created_by'] = $request->user()?->id;

        $productSku = ProductSku::query()->create($payload);
        $this->barcodeAssetService->syncProductSku($productSku);
        $productSku->refresh();

        $this->activityLogService->log(
            moduleName: 'product_skus',
            record: $productSku,
            action: 'created',
            newValue: $productSku->fresh(['product'])?->toArray(),
            description: "SKU {$productSku->sku_code} created for product {$product->product_code}",
        );

        return redirect()
            ->route('product-skus.show', $productSku)
            ->with('status', "SKU {$productSku->sku_code} created successfully.");
    }

    public function show(Request $request, ProductSku $productSku): View
    {
        return view('product-skus.show', [
            'pageTitle' => 'SKU Detail',
            'pageSubtitle' => 'Ringkasan SKU retail, barcode, dan metadata varian produk.',
            'productSku' => $productSku->load(['product', 'creator', 'packagings']),
            'canManageProducts' => $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false,
        ]);
    }

    public function edit(Request $request, ProductSku $productSku): View
    {
        return view('product-skus.edit', [
            'pageTitle' => 'Edit Product SKU',
            'pageSubtitle' => "Update data SKU {$productSku->sku_code}.",
            'product' => $productSku->product()->firstOrFail(),
            'productSku' => $productSku,
            'formAction' => route('product-skus.update', $productSku),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update SKU',
            'canManageProducts' => $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false,
            'barcodeTypeOptions' => BarcodeType::options(),
        ]);
    }

    public function update(ProductSkuRequest $request, ProductSku $productSku): RedirectResponse
    {
        $oldValue = $productSku->toArray();
        $productSku->update($request->validated());
        $this->barcodeAssetService->syncProductSku($productSku);
        $productSku->refresh();

        $this->activityLogService->log(
            moduleName: 'product_skus',
            record: $productSku,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $productSku->fresh(['product'])?->toArray(),
            description: "SKU {$productSku->sku_code} updated",
        );

        return redirect()
            ->route('product-skus.show', $productSku)
            ->with('status', "SKU {$productSku->sku_code} updated successfully.");
    }

    public function destroy(ProductSku $productSku): RedirectResponse
    {
        $oldValue = $productSku->toArray();
        $skuCode = $productSku->sku_code;
        $product = $productSku->product;
        $this->barcodeAssetService->deleteBarcodeAsset($productSku);
        $productSku->delete();

        $this->activityLogService->log(
            moduleName: 'product_skus',
            record: $productSku,
            action: 'deleted',
            oldValue: $oldValue,
            description: "SKU {$skuCode} deleted",
        );

        return redirect()
            ->route('products.skus.index', $product)
            ->with('status', "SKU {$skuCode} deleted successfully.");
    }
}
