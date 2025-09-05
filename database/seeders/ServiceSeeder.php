<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear servicios de ejemplo
        $services = [
            [
                'name' => 'Mantenimiento preventivo',
                'description' => 'Mantenimiento rutinario para prevenir fallas en equipos',
                'price' => 150.00,
                'duration' => 60, // en minutos
                'active' => true
            ],
            [
                'name' => 'Reparación de emergencia',
                'description' => 'Servicio de reparación urgente para equipos con fallas',
                'price' => 250.00,
                'duration' => 120, // en minutos
                'active' => true
            ],
            [
                'name' => 'Instalación de equipos',
                'description' => 'Instalación y configuración de nuevos equipos',
                'price' => 200.00,
                'duration' => 180, // en minutos
                'active' => true
            ],
            [
                'name' => 'Asesoría técnica',
                'description' => 'Consultoría y asesoramiento técnico especializado',
                'price' => 100.00,
                'duration' => 45, // en minutos
                'active' => true
            ],
            [
                'name' => 'Diagnóstico especializado',
                'description' => 'Diagnóstico detallado de problemas complejos',
                'price' => 120.00,
                'duration' => 90, // en minutos
                'active' => true
            ],
            [
                'name' => 'Reparación de Aire Acondicionado',
                'description' => 'Servicio de reparación y mantenimiento para sistemas de aire acondicionado',
                'price' => 280.00,
                'duration' => 150, // en minutos
                'active' => true
            ],
            [
                'name' => 'Mantenimiento de Refrigeradores',
                'description' => 'Servicio de mantenimiento preventivo y correctivo para refrigeradores',
                'price' => 220.00,
                'duration' => 120, // en minutos
                'active' => true
            ],
            [
                'name' => 'Instalación de Lavadoras',
                'description' => 'Servicio de instalación y configuración de lavadoras',
                'price' => 180.00,
                'duration' => 90, // en minutos
                'active' => true
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
