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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->enum('movement_type', ['entry', 'exit', 'adjustment']);
            $table->integer('quantity');
            $table->integer('previous_quantity');
            $table->integer('new_quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->nullableMorphs('reference'); // Referencia polimórfica (compra, servicio, etc)
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'movement_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
