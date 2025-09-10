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
        Schema::create('provider_inventory_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->string('provider_code')->nullable()->comment('Código del producto usado por el proveedor');
            $table->decimal('provider_price', 10, 2)->nullable()->comment('Precio ofrecido por el proveedor');
            $table->integer('lead_time')->nullable()->comment('Tiempo de entrega en días');
            $table->timestamps();
            
            $table->unique(['provider_id', 'inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_inventory_item');
    }
};
