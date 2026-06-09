<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('products')
                ->nullOnDelete();
            $table->foreignId('product_sku_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_skus')
                ->nullOnDelete();
            $table->unsignedInteger('lead_time_days')->nullable()->after('minimum_order_kg');
            $table->string('packaging_type')->nullable()->after('lead_time_days');
            $table->boolean('is_active')->default(true)->after('packaging_type');
            $table->text('notes')->nullable()->after('is_active');
            $table->index(['product_id', 'product_sku_id']);
        });

        $products = DB::table('products')
            ->select('id', 'product_name')
            ->get();

        $productIdsByName = $products
            ->mapWithKeys(fn ($product) => [$this->normalizeKey($product->product_name) => $product->id])
            ->all();

        DB::table('supplier_products')
            ->orderBy('id')
            ->get(['id', 'product_name'])
            ->each(function ($row) use ($productIdsByName) {
                $productId = $this->resolveLegacyProductId($row->product_name, $productIdsByName);

                if ($productId === null) {
                    return;
                }

                DB::table('supplier_products')
                    ->where('id', $row->id)
                    ->update(['product_id' => $productId]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'product_sku_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['product_sku_id']);
            $table->dropColumn([
                'product_id',
                'product_sku_id',
                'lead_time_days',
                'packaging_type',
                'is_active',
                'notes',
            ]);
        });
    }

    private function resolveLegacyProductId(?string $legacyName, array $productIdsByName): ?int
    {
        $normalized = $this->normalizeKey($legacyName);

        if ($normalized === '') {
            return null;
        }

        if (array_key_exists($normalized, $productIdsByName)) {
            return $productIdsByName[$normalized];
        }

        $aliases = [
            'clove stem' => 'clove',
            'cinnamon cut' => 'cinnamon',
            'cinnamon stick' => 'cinnamon',
            'vanilla bean' => 'vanilla',
            'vanilla beans' => 'vanilla',
        ];

        $alias = $aliases[$normalized] ?? null;

        return $alias !== null && array_key_exists($alias, $productIdsByName)
            ? $productIdsByName[$alias]
            : null;
    }

    private function normalizeKey(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized) ?? '';

        return trim($normalized);
    }
};
