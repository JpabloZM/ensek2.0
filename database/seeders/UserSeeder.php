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
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]);
        
        // Crear usuario técnico
        User::create([
            'name' => 'Técnico Demo',
            'email' => 'tecnico@empresa.com',
            'password' => Hash::make('password'),
            'role_id' => Role::where('name', 'technician')->first()->id,
            'phone' => '123456789',
        ]);
    }
}
