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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('destination_country')->nullable()->after('client_id');
            $table->string('destination_port')->nullable()->after('destination_country');
            $table->string('shipment_mode')->nullable()->after('destination_port');
            $table->decimal('local_logistics_cost', 14, 2)->default(0)->after('gross_margin');
            $table->decimal('export_document_cost', 14, 2)->default(0)->after('local_logistics_cost');
            $table->decimal('forwarding_cost', 14, 2)->default(0)->after('export_document_cost');
            $table->decimal('freight_cost', 14, 2)->default(0)->after('forwarding_cost');
            $table->decimal('insurance_cost', 14, 2)->default(0)->after('freight_cost');
            $table->decimal('compliance_cost', 14, 2)->default(0)->after('insurance_cost');
            $table->decimal('destination_cost', 14, 2)->default(0)->after('compliance_cost');
            $table->decimal('misc_cost', 14, 2)->default(0)->after('destination_cost');
            $table->decimal('total_additional_cost', 14, 2)->default(0)->after('misc_cost');
            $table->decimal('net_profit', 14, 2)->default(0)->after('total_additional_cost');
            $table->decimal('net_margin', 7, 2)->default(0)->after('net_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'destination_country',
                'destination_port',
                'shipment_mode',
                'local_logistics_cost',
                'export_document_cost',
                'forwarding_cost',
                'freight_cost',
                'insurance_cost',
                'compliance_cost',
                'destination_cost',
                'misc_cost',
                'total_additional_cost',
                'net_profit',
                'net_margin',
            ]);
        });
    }
};
