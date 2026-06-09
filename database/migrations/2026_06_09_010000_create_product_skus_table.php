<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku_code')->unique();
            $table->string('variant_name');
            $table->string('brand_name')->nullable();
            $table->decimal('net_weight', 14, 2)->nullable();
            $table->string('weight_unit', 20)->default('G');
            $table->string('sellable_unit', 50)->default('EACH');
            $table->string('barcode_type', 50)->nullable();
            $table->string('gtin', 50)->nullable();
            $table->string('barcode_number', 50)->nullable()->unique();
            $table->boolean('is_retail_sellable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_active']);
            $table->index('is_retail_sellable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};
