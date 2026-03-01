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
        if (!Schema::hasTable('batches')) {
            return;
        }

        if (Schema::hasColumn('batches', 'purchase_item_id')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_item_id')->nullable()->after('product_ID');
            $table->index('purchase_item_id');

            $table->foreign('purchase_item_id')
                ->references('purchase_item_id')
                ->on('purchase_items')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('batches')) {
            return;
        }

        if (!Schema::hasColumn('batches', 'purchase_item_id')) {
            return;
        }

        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['purchase_item_id']);
            $table->dropIndex(['purchase_item_id']);
            $table->dropColumn('purchase_item_id');
        });
    }
};
