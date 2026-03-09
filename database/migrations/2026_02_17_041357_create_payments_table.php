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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('purchase_id');
            $table->date('payment_date');
            $table->decimal('amount_paid', 18, 2);
            $table->string('payment_method');
            $table->string('reference_number')->nullable()->unique();
            $table->foreign('purchase_id')->references('purchase_id')->on('purchases')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
