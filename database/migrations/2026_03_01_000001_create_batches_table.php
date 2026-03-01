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
        Schema::create('batches', function (Blueprint $table) {
            $table->bigIncrements('batch_ID');

            // NOTE: This matches the existing naming convention used in the app (product_ID).
            $table->unsignedBigInteger('product_ID');

            $table->string('batch_code')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiration_date');

            // boxes/pieces, etc.
            $table->unsignedInteger('quantity')->default(0);

            $table->timestamps();

            $table->index('product_ID');
            $table->index('expiration_date');

            // Prevent duplicates for the core rule: one batch per product + expiration date
            $table->unique(['product_ID', 'expiration_date'], 'batches_product_expiration_unique');

            // Foreign key intentionally omitted because this repo does not include
            // the products/inventory table migrations (adding FK could break fresh installs).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
