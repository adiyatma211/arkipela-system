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
        Schema::create('arkipela_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('group_key');
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('attributes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group_key', 'code']);
            $table->index(['group_key', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arkipela_parameters');
    }
};
