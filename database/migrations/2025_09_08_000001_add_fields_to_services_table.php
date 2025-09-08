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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->after('price')->default(0.00)->comment('Tasa de impuesto en porcentaje');
            $table->text('special_requirements')->after('duration')->nullable()->comment('Requisitos especiales para este servicio');
            $table->text('materials_included')->after('special_requirements')->nullable()->comment('Materiales incluidos en el servicio');
            $table->boolean('requires_technician_approval')->after('materials_included')->default(false)->comment('Si requiere aprobación de un técnico especializado');
            $table->softDeletes(); // Añade deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'tax_rate',
                'special_requirements',
                'materials_included',
                'requires_technician_approval'
            ]);
            $table->dropSoftDeletes(); // Elimina la columna deleted_at
        });
    }
};
