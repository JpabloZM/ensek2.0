<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Administrador',
            'description' => 'Acceso completo a todas las funciones del sistema',
        ]);
        
        Role::create([
            'name' => 'Técnico',
            'description' => 'Acceso para administrar servicios, solicitudes y horarios',
        ]);
        
        Role::create([
            'name' => 'Cliente',
            'description' => 'Acceso limitado para crear y ver solicitudes de servicio',
        ]);
    }
}
