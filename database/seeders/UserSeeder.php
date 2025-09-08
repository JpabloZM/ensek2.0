<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@empresa.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Administrador')->first()->id,
        ]);
        
        // Crear usuario técnico
        User::create([
            'name' => 'Técnico Demo',
            'email' => 'tecnico@empresa.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Técnico')->first()->id,
            'phone' => '123456789',
        ]);
        
        // Crear usuarios clientes de ejemplo
        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@empresa.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'Cliente')->first()->id,
            'phone' => '987654321',
        ]);
        
        // Crear técnicos adicionales
        $technicians = [
            [
                'name' => 'Carlos Técnico',
                'email' => 'carlos@empresa.com',
                'password' => Hash::make('password'),
                'phone' => '555666777',
            ],
            [
                'name' => 'María Técnico',
                'email' => 'maria@empresa.com',
                'password' => Hash::make('password'),
                'phone' => '777888999',
            ],
        ];
        
        foreach ($technicians as $technicianData) {
            User::create(array_merge($technicianData, [
                'role_id' => Role::where('name', 'Técnico')->first()->id,
            ]));
        }
    }
}
