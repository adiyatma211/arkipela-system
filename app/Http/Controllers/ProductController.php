<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Enums\UserPermission;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ActivityLogService;
use App\Services\CodeGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
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
        $canManageProducts = $request->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false;

        $filters = [
            'search' => $request->string('search')->toString(),
            'status' => $request->string('status')->toString(),
            'category' => $request->string('category')->toString(),
        ];

        $products = Product::query()
            ->with('creator')
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('product_code', 'like', "%{$search}%")
                        ->orWhere('product_name', 'like', "%{$search}%")
                        ->orWhere('scientific_name', 'like', "%{$search}%")
                        ->orWhere('origin_area', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'], fn ($query, $category) => $query->where('category', 'like', "%{$category}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'pageTitle' => 'Product Master',
            'pageSubtitle' => 'Kelola commodity master ArkipelaSpice sebagai source of truth untuk supplier, SKU, dan order berikutnya.',
            'products' => $products,
            'filters' => $filters,
            'statusOptions' => ProductStatus::options(),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'canManageProducts' => $canManageProducts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('products.create', [
            'pageTitle' => 'Add Product',
            'pageSubtitle' => 'Input commodity master baru yang akan dipakai lintas modul.',
            'product' => new Product([
                'default_unit' => 'KG',
                'status' => ProductStatus::ACTIVE->value,
            ]),
            'statusOptions' => ProductStatus::options(),
            'formAction' => route('products.store'),
            'formMethod' => 'POST',
            'submitLabel' => 'Save Product',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $payload['product_code'] = $this->codeGeneratorService->generateProductCode();
        $payload['created_by'] = $request->user()?->id;

        $product = Product::query()->create($payload);

        $this->activityLogService->log(
            moduleName: 'products',
            record: $product,
            action: 'created',
            newValue: $product->fresh()?->toArray(),
            description: "Product {$product->product_code} created",
        );

        return redirect()
            ->route('products.show', $product)
            ->with('status', "Product {$product->product_code} created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('products.show', [
            'pageTitle' => 'Product Detail',
            'pageSubtitle' => 'Ringkasan master commodity yang menjadi fondasi supplier mapping dan SKU retail.',
            'product' => $product->load(['creator', 'skus']),
            'statusBadgeMap' => $this->statusBadgeMap(),
            'statusLabelMap' => $this->statusLabelMap(),
            'canManageProducts' => request()->user()?->hasPermission(UserPermission::PRODUCTS_MANAGE->value) ?? false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', [
            'pageTitle' => 'Edit Product',
            'pageSubtitle' => "Update data master untuk {$product->product_code}.",
            'product' => $product,
            'statusOptions' => ProductStatus::options(),
            'formAction' => route('products.update', $product),
            'formMethod' => 'PUT',
            'submitLabel' => 'Update Product',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $oldValue = $product->toArray();
        $product->update($request->validated());

        $this->activityLogService->log(
            moduleName: 'products',
            record: $product,
            action: 'updated',
            oldValue: $oldValue,
            newValue: $product->fresh()?->toArray(),
            description: "Product {$product->product_code} updated",
        );

        return redirect()
            ->route('products.show', $product)
            ->with('status', "Product {$product->product_code} updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $oldValue = $product->toArray();
        $productCode = $product->product_code;
        $product->delete();

        $this->activityLogService->log(
            moduleName: 'products',
            record: $product,
            action: 'deleted',
            oldValue: $oldValue,
            description: "Product {$productCode} deleted",
        );

        return redirect()
            ->route('products.index')
            ->with('status', "Product {$productCode} deleted successfully.");
    }

    private function statusBadgeMap(): array
    {
        return [
            ProductStatus::ACTIVE->value => 'bg-success',
            ProductStatus::INACTIVE->value => 'bg-secondary',
        ];
    }

    private function statusLabelMap(): array
    {
        return collect(ProductStatus::options())
            ->pluck('label', 'value')
            ->all();
    }
}
