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
        // First, check if foreign key constraint exists and drop it
        try {
            Schema::table('sales_transactions', function (Blueprint $table) {
                $table->dropForeign(['sales_id']);
            });
        } catch (Exception $e) {
            // Foreign key constraint doesn't exist, continue
        }

        // Update existing sales records to use user_id
        $salesRecords = DB::table('sales')->get();
        foreach ($salesRecords as $sales) {
            // Find user by name or create a mapping
            $user = DB::table('users')->where('name', $sales->name)->first();
            if ($user) {
                // Update sales_transactions to use user_id instead of sales_id
                DB::table('sales_transactions')
                    ->where('sales_id', $sales->id)
                    ->update(['sales_id' => $user->id]);
            } else {
                // If no matching user found, use the first available user
                $firstUser = DB::table('users')->first();
                if ($firstUser) {
                    DB::table('sales_transactions')
                        ->where('sales_id', $sales->id)
                        ->update(['sales_id' => $firstUser->id]);
                }
            }
        }

        // Drop the old sales table
        Schema::dropIfExists('sales');

        // Create new sales table with user_id as primary key
        Schema::create('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // This will be user_id
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add foreign key constraint to users table
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create sales records for all users that have transactions
        $userIds = DB::table('sales_transactions')->distinct()->pluck('sales_id');
        foreach ($userIds as $userId) {
            $user = DB::table('users')->find($userId);
            if ($user) {
                DB::table('sales')->insert([
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone ?? '',
                    'address' => $user->address ?? '',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Recreate foreign key constraint in sales_transactions
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->dropForeign(['sales_id']);
        });

        // Drop the sales table
        Schema::dropIfExists('sales');

        // Recreate original sales table
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate foreign key constraint
        Schema::table('sales_transactions', function (Blueprint $table) {
            $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
        });
    }
};