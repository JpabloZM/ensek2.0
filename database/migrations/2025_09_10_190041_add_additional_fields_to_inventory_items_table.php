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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('unit_of_measure')->nullable()->after('minimum_stock');
            $table->string('barcode')->nullable()->after('unit_of_measure');
            $table->string('sku')->nullable()->after('barcode');
            $table->integer('reorder_point')->default(0)->after('sku');
            $table->boolean('is_active')->default(true)->after('reorder_point');
            $table->date('last_purchase_date')->nullable()->after('is_active');
            $table->decimal('last_purchase_price', 10, 2)->nullable()->after('last_purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_of_measure',
                'barcode',
                'sku',
                'reorder_point',
                'is_active',
                'last_purchase_date',
                'last_purchase_price',
            ]);
        });
    }
};
