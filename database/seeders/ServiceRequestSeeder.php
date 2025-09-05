<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya hay servicios en la base de datos
        $services = Service::all();
        if ($services->isEmpty()) {
            $this->command->info('No hay servicios disponibles. Por favor, cree algunos servicios primero.');
            return;
        }

        // Obtener un usuario administrador para asignarlo como creador de las solicitudes
        $admin = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$admin) {
            $this->command->info('No se encontró un usuario administrador. Por favor, cree uno primero.');
            return;
        }

        // Crear solicitud 1: Urgente, reparación de aire acondicionado
        ServiceRequest::create([
            'service_id' => $services->where('name', 'like', '%Aire%')->first() ? 
                $services->where('name', 'like', '%Aire%')->first()->id : 
                $services->first()->id,
            'user_id' => $admin->id,
            'client_name' => 'María González',
            'client_phone' => '555-123-4567',
            'client_email' => 'maria@example.com',
            'description' => 'El aire acondicionado hace un ruido fuerte y no enfría. Es urgente porque tenemos un bebé pequeño y hace mucho calor.',
            'address' => 'Av. Principal 123, Colonia Centro',
            'status' => 'pendiente',
            'notes' => 'Cliente preferente, atender lo antes posible',
            'created_at' => now()->subDays(2), // Solicitud de hace 2 días
        ]);

        // Crear solicitud 2: Mantenimiento de refrigerador
        ServiceRequest::create([
            'service_id' => $services->where('name', 'like', '%Refrigera%')->first() ? 
                $services->where('name', 'like', '%Refrigera%')->first()->id : 
                $services->random()->id,
            'user_id' => $admin->id,
            'client_name' => 'Juan Pérez',
            'client_phone' => '555-987-6543',
            'client_email' => 'juanperez@gmail.com',
            'description' => 'El refrigerador no está enfriando lo suficiente y los alimentos se están echando a perder. La temperatura interna está por encima de lo normal.',
            'address' => 'Calle Secundaria 456, Residencial Las Palmas',
            'status' => 'pendiente',
            'notes' => 'Cliente nuevo, verificar garantía',
            'created_at' => now()->subDay(), // Solicitud de ayer
        ]);

        // Crear solicitud 3: Instalación de lavadora
        ServiceRequest::create([
            'service_id' => $services->where('name', 'like', '%Lavad%')->first() ? 
                $services->where('name', 'like', '%Lavad%')->first()->id : 
                $services->random()->id,
            'user_id' => $admin->id,
            'client_name' => 'Roberto Sánchez',
            'client_phone' => '555-555-5555',
            'client_email' => 'roberto.sanchez@outlook.com',
            'description' => 'Necesito instalación y configuración de lavadora nueva marca Samsung. Ya tengo las conexiones de agua y drenaje listas.',
            'address' => 'Avenida del Bosque 789, Fraccionamiento Los Pinos',
            'status' => 'pendiente',
            'created_at' => now(), // Solicitud de hoy
        ]);

        $this->command->info('Se han creado 3 solicitudes de servicio de muestra correctamente.');
    }
}
