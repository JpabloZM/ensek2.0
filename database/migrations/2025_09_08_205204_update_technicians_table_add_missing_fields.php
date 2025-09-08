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
            // Drop columns we're going to rename or modify
            $table->dropColumn(['specialty_id', 'skills', 'years_experience']);
            
            // Add new columns for technician profile
            $table->string('specialization')->nullable()->after('user_id');
            $table->integer('experience_years')->default(0)->after('specialization');
            $table->string('certification')->nullable()->after('experience_years');
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active')->after('profile_image');
            $table->decimal('hourly_rate', 8, 2)->default(0)->after('status');
            $table->decimal('rating', 3, 1)->default(0)->after('hourly_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            $table->dropColumn([
                'specialization', 
                'experience_years', 
                'certification', 
                'status',
                'hourly_rate',
                'rating'
            ]);
            
            $table->string('specialty_id')->nullable();
            $table->text('skills')->nullable();
            $table->integer('years_experience')->default(0);
        });
    }
};
