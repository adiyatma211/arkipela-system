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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_code')->nullable()->after('supplier_id');
            $table->string('hs_code')->nullable()->after('product_name');
            $table->unsignedInteger('quantity_pcs')->nullable()->after('quantity_kg');
            $table->string('quantity_unit', 20)->nullable()->after('quantity_pcs');
            $table->unsignedInteger('pieces_per_package')->nullable()->after('quantity_unit');
            $table->unsignedInteger('package_count')->nullable()->after('pieces_per_package');
            $table->string('package_type')->nullable()->after('package_count');
            $table->string('outer_package_type')->nullable()->after('package_type');
            $table->decimal('length_cm', 10, 2)->nullable()->after('outer_package_type');
            $table->decimal('width_cm', 10, 2)->nullable()->after('length_cm');
            $table->decimal('height_cm', 10, 2)->nullable()->after('width_cm');
            $table->decimal('net_weight_kg', 14, 2)->nullable()->after('height_cm');
            $table->decimal('gross_weight_kg', 14, 2)->nullable()->after('net_weight_kg');
            $table->text('package_notes')->nullable()->after('gross_weight_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_code',
                'hs_code',
                'quantity_pcs',
                'quantity_unit',
                'pieces_per_package',
                'package_count',
                'package_type',
                'outer_package_type',
                'length_cm',
                'width_cm',
                'height_cm',
                'net_weight_kg',
                'gross_weight_kg',
                'package_notes',
            ]);
        });
    }
};
