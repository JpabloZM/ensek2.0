<?php

namespace Database\Seeders;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TechnicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el usuario técnico
        $technicianUser = User::where('email', 'tecnico@empresa.com')->first();
        
        if ($technicianUser) {
            Technician::create([
                'user_id' => $technicianUser->id,
                'specialty' => 'Electrónica',
                'skills' => 'Reparación de equipos electrónicos, instalación de sistemas, mantenimiento preventivo',
                'active' => true,
                'availability' => 'full_time'
            ]);
        }
    }
}
