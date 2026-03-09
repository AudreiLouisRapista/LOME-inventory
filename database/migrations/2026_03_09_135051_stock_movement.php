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
    Schema::create('stock_movements', function (Blueprint $table) {
        // Primary Key
        $table->id('StockMovementID'); 
        
        // Foreign Keys (BigInteger is standard for IDs in Laravel)
        $table->unsignedBigInteger('Product_ID');
        $table->unsignedBigInteger('Batch_ID')->nullable();
        $table->unsignedBigInteger('Purchase_id')->nullable();
        
        // Movement Logic
        $table->enum('MovementType', ['IN', 'OUT', 'ADJUSTMENT', 'RETURN']);
        $table->integer('Quantity'); // How many units moved?
        $table->integer('Balance_After'); // Snapshot of total stock after move
        
        // Info
        $table->string('Remarks')->nullable();
        
        // Timestamps (This covers your Created_At and CreatedAt)
        $table->timestamps(); 

        // Optional: Add Foreign Key constraints if your tables exist
        // $table->foreign('Product_ID')->references('product_ID')->on('products');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
