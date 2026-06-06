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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('product_name');
            $table->text('specification')->nullable();
            $table->decimal('quantity_kg', 14, 2);
            $table->decimal('selling_price', 14, 2);
            $table->decimal('buying_price', 14, 2)->default(0);
            $table->decimal('line_total_sales', 14, 2)->default(0);
            $table->decimal('line_total_buying', 14, 2)->default(0);
            $table->decimal('line_profit', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['order_id', 'supplier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
