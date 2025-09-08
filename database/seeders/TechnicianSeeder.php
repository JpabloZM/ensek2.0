<?php

namespace Database\Seeders;

use App\Models\Technician;
use App\Models\User;
use App\Models\Skill;
use App\Models\TechnicianAvailability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TechnicianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios técnicos
        $technicianUsers = User::whereHas('role', function($query) {
            $query->where('name', 'Técnico');
        })->get();
        
        // Si no hay usuarios técnicos, buscar si existe el usuario tecnico@empresa.com
        if ($technicianUsers->isEmpty()) {
            $this->command->info('No se encontraron usuarios técnicos, verificando usuario existente');
            
            $existingUser = User::where('email', 'tecnico@empresa.com')->first();
            
            if ($existingUser) {
                $this->command->info('Usuario técnico encontrado, actualizando su rol');
                
                // Verificar que el rol de técnico existe
                $technicianRole = \App\Models\Role::where('name', 'Técnico')->first();
                if (!$technicianRole) {
                    $this->command->error('No se encontró el rol de técnico, asegúrate de que los roles estén creados primero');
                    return;
                }
                
                // Actualizar el rol del usuario existente
                $existingUser->update(['role_id' => $technicianRole->id]);
                $technicianUsers = collect([$existingUser]);
            } else {
                $this->command->info('No existe usuario técnico, creando uno nuevo');
                
                // Verificar que el rol de técnico existe
                $technicianRole = \App\Models\Role::where('name', 'Técnico')->first();
                if (!$technicianRole) {
                    $this->command->error('No se encontró el rol de técnico, asegúrate de que los roles estén creados primero');
                    return;
                }
                
                // Crear un usuario técnico por defecto
                $technicianUser = User::create([
                    'name' => 'Técnico Predeterminado',
                    'email' => 'tecnico@empresa.com',
                    'password' => bcrypt('password'),
                    'role_id' => $technicianRole->id,
                ]);
                
                $technicianUsers = collect([$technicianUser]);
            }
        }
        
        // Obtener todas las habilidades
        $skills = Skill::all();
        
        // Si no hay habilidades, salir temprano
        if ($skills->isEmpty()) {
            $this->command->info('No se encontraron habilidades, saltando la asignación de habilidades');
        }
        
        // Crear perfiles de técnico
        foreach ($technicianUsers as $user) {
            $technician = Technician::create([
                'user_id' => $user->id,
                'specialization' => $this->getRandomSpecialization(),
                'experience_years' => rand(1, 15),
                'certification' => $this->getRandomCertification(),
                'profile_image' => 'technicians/default.png',
                'status' => 'active',
                'bio' => 'Técnico profesional con experiencia en soporte de TI y reparación de equipos.',
                'hourly_rate' => rand(2000, 5000) / 100,
                'rating' => rand(30, 50) / 10,
            ]);
            
            // Añadir habilidades aleatorias a cada técnico (3-6 habilidades)
            if (!$skills->isEmpty()) {
                $randomSkills = $skills->random(min(rand(3, 6), $skills->count()));
                foreach ($randomSkills as $skill) {
                    $technician->skills()->attach($skill->id, ['proficiency_level' => rand(1, 5)]);
                }
            }
            
            // Crear disponibilidad para cada técnico
            $this->createAvailabilityForTechnician($technician);
        }
    }
    
    /**
     * Obtener una especialización aleatoria
     */
    private function getRandomSpecialization()
    {
        $specializations = [
            'Reparación de Hardware', 
            'Soporte de Software', 
            'Redes', 
            'Seguridad Informática',
            'Sistemas Operativos',
            'Servidores',
        ];
        
        return $specializations[array_rand($specializations)];
    }
    
    /**
     * Obtener una certificación aleatoria
     */
    private function getRandomCertification()
    {
        $certifications = [
            'CompTIA A+',
            'Microsoft Certified Professional',
            'CCNA',
            'MCSA',
            'Linux Professional Institute',
            'Ninguna',
        ];
        
        return $certifications[array_rand($certifications)];
    }
    
    /**
     * Crear disponibilidad semanal para un técnico
     */
    private function createAvailabilityForTechnician(Technician $technician)
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        foreach ($daysOfWeek as $day) {
            TechnicianAvailability::create([
                'technician_id' => $technician->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_available' => true,
            ]);
        }
        
        // Algunos técnicos trabajan los fines de semana
        if (rand(0, 1)) {
            TechnicianAvailability::create([
                'technician_id' => $technician->id,
                'day_of_week' => 'Saturday',
                'start_time' => '10:00:00',
                'end_time' => '15:00:00',
                'is_available' => true,
            ]);
        }
    }
}
