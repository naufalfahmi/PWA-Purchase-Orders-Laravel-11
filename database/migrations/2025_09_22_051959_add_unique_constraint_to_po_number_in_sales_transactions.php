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
        Schema::table('sales_transactions', function (Blueprint $table) {
            // First, clean up any duplicate po_numbers
            $duplicates = DB::table('sales_transactions')
                ->select('po_number', DB::raw('COUNT(*) as count'))
                ->groupBy('po_number')
                ->having('count', '>', 1)
                ->get();

            foreach ($duplicates as $duplicate) {
                // Keep only the first record, delete the rest
                $records = DB::table('sales_transactions')
                    ->where('po_number', $duplicate->po_number)
                    ->orderBy('id')
                    ->get();

                if ($records->count() > 1) {
                    $keepId = $records->first()->id;
                    DB::table('sales_transactions')
                        ->where('po_number', $duplicate->po_number)
                        ->where('id', '!=', $keepId)
                        ->delete();
                }
            }

            // Add unique constraint
            $table->unique('po_number', 'unique_po_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropUnique('unique_po_number');
        });
    }
};