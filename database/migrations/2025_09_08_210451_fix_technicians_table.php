<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar y corregir la estructura de la tabla de técnicos
        
        // Primero verificamos que columnas existen en la tabla
        $columns = DB::select("SHOW COLUMNS FROM technicians");
        $columnNames = array_map(function($column) {
            return $column->Field;
        }, $columns);
        
        Schema::table('technicians', function (Blueprint $table) use ($columnNames) {
            // Si no existe la columna 'specialization'
            if (!in_array('specialization', $columnNames)) {
                $table->string('specialization')->nullable()->after('user_id');
            }
            
            // Si no existe la columna 'experience_years'
            if (!in_array('experience_years', $columnNames)) {
                $table->integer('experience_years')->default(0)->after('specialization');
            }
            
            // Si no existe la columna 'certification'
            if (!in_array('certification', $columnNames)) {
                $table->string('certification')->nullable()->after('experience_years');
            }
            
            // Si no existe la columna 'status'
            if (!in_array('status', $columnNames)) {
                $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active')->after('profile_image');
            }
            
            // Si no existe la columna 'hourly_rate'
            if (!in_array('hourly_rate', $columnNames)) {
                $table->decimal('hourly_rate', 8, 2)->default(0)->after('status');
            }
            
            // Si no existe la columna 'rating'
            if (!in_array('rating', $columnNames)) {
                $table->decimal('rating', 3, 1)->default(0)->after('hourly_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacer nada en el rollback
    }
};
