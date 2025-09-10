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
            $table->id();
            $table->foreignId('provider_id')->constrained()->onDelete('restrict');
            $table->string('reference_number')->nullable();
            $table->date('purchase_date');
            $table->date('receipt_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamps();
            
            $table->index(['provider_id', 'status']);
            $table->index('purchase_date');
            $table->index('reference_number');
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
