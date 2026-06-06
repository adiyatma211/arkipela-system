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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_code')->unique();
            $table->string('company_name');
            $table->string('country')->default('Indonesia');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('pic_name')->nullable();
            $table->string('pic_position')->nullable();
            $table->string('pic_email')->nullable();
            $table->string('pic_whatsapp')->nullable();
            $table->string('interested_products')->nullable();
            $table->decimal('target_quantity_kg', 14, 2)->nullable();
            $table->decimal('target_price', 14, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->string('preferred_incoterm')->nullable();
            $table->string('preferred_payment_term')->nullable();
            $table->string('status')->default('lead');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'country']);
            $table->index(['company_name', 'pic_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
