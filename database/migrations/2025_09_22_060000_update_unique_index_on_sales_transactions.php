<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old unique index on po_number (prevents multi-product per PO)
        try {
            Schema::table('sales_transactions', function (Blueprint $table) {
                $table->dropUnique('unique_po_number');
            });
        } catch (\Throwable $e) {
            // index might not exist; ignore
        }

        // Ensure columns indexed appropriately, then add composite unique (po_number, product_id)
        Schema::table('sales_transactions', function (Blueprint $table) {
            // Add composite unique to prevent duplicate product rows for same PO
            $table->unique(['po_number', 'product_id'], 'unique_po_product');
        });
    }

    public function down(): void
    {
        // Revert composite unique and restore single unique on po_number
        try {
            Schema::table('sales_transactions', function (Blueprint $table) {
                $table->dropUnique('unique_po_product');
            });
        } catch (\Throwable $e) {
        }

        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->unique('po_number', 'unique_po_number');
        });
    }
};


