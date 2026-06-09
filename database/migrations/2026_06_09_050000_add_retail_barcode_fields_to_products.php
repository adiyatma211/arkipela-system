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
        Schema::table('product_skus', function (Blueprint $table) {
            $table->string('upc', 20)->nullable()->after('gtin');
            $table->string('ean', 20)->nullable()->after('upc');
            $table->string('barcode_image_path')->nullable()->after('barcode_number');
            $table->index('upc');
            $table->index('ean');
        });

        Schema::table('product_packagings', function (Blueprint $table) {
            $table->string('upc', 20)->nullable()->after('gtin');
            $table->string('ean', 20)->nullable()->after('upc');
            $table->string('barcode_image_path')->nullable()->after('barcode_number');
            $table->index('upc');
            $table->index('ean');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_packagings', function (Blueprint $table) {
            $table->dropIndex(['upc']);
            $table->dropIndex(['ean']);
            $table->dropColumn(['upc', 'ean', 'barcode_image_path']);
        });

        Schema::table('product_skus', function (Blueprint $table) {
            $table->dropIndex(['upc']);
            $table->dropIndex(['ean']);
            $table->dropColumn(['upc', 'ean', 'barcode_image_path']);
        });
    }
};
