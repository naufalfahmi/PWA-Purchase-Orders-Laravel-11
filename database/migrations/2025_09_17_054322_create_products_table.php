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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity_per_carton')->default(0);
            $table->string('stock_unit')->default('pcs');
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->integer('pieces_per_carton')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};