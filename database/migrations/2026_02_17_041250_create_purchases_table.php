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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id("purchase_id");
            $table->unsignedBigInteger('supplier_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('gross_amount', 18, 2);
            $table->decimal('vat_amount', 18, 2);
            $table->decimal('net_amount', 18, 2);
            $table->enum('status', ['paid', 'partial', 'unpaid', 'overdue'])->default('unpaid');
            $table->timestamps();
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
