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
        Schema::table('technicians', function (Blueprint $table) {
            // Cambia el campo specialty a specialty_id para ser consistente
            $table->renameColumn('specialty', 'specialty_id');
            
            // Elimina el campo availability existente
            $table->dropColumn('availability');
            
            // Añadir nuevos campos
            $table->string('title')->nullable()->after('user_id');
            $table->text('bio')->nullable()->after('title');
            $table->json('certifications')->nullable()->after('skills');
            $table->enum('employment_type', ['full_time', 'part_time', 'contractor'])->default('full_time')->after('certifications');
            $table->date('hire_date')->nullable()->after('employment_type');
            $table->integer('years_experience')->default(0)->after('hire_date');
            $table->string('profile_image')->nullable()->after('years_experience');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            $table->renameColumn('specialty_id', 'specialty');
            $table->string('availability')->default('full_time')->comment('full_time, part_time, on_call');
            
            $table->dropColumn([
                'title',
                'bio',
                'certifications',
                'employment_type',
                'hire_date',
                'years_experience',
                'profile_image'
            ]);
        });
    }
};
