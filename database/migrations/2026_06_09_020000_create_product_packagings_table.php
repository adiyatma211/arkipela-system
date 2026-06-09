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
        Schema::create('product_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sku_id')->constrained('product_skus')->cascadeOnDelete();
            $table->string('level', 20);
            $table->unsignedInteger('units_per_pack')->nullable();
            $table->string('barcode_type', 50)->nullable();
            $table->string('gtin', 50)->nullable();
            $table->string('barcode_number', 50)->nullable()->unique();
            $table->decimal('length', 14, 2)->nullable();
            $table->decimal('width', 14, 2)->nullable();
            $table->decimal('height', 14, 2)->nullable();
            $table->string('dimension_unit', 20)->default('CM');
            $table->decimal('net_weight', 14, 2)->nullable();
            $table->decimal('gross_weight', 14, 2)->nullable();
            $table->boolean('is_default_for_level')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_sku_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packagings');
    }
};
