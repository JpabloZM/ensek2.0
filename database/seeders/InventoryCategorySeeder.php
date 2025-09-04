<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear categorías de inventario
        $categories = [
            [
                'name' => 'Herramientas',
                'description' => 'Herramientas manuales y eléctricas'
            ],
            [
                'name' => 'Repuestos',
                'description' => 'Repuestos para diferentes equipos'
            ],
            [
                'name' => 'Materiales',
                'description' => 'Materiales consumibles para servicios'
            ],
            [
                'name' => 'Equipos',
                'description' => 'Equipos completos para instalación'
            ],
            [
                'name' => 'Accesorios',
                'description' => 'Accesorios y complementos'
            ],
        ];

        foreach ($categories as $category) {
            InventoryCategory::create($category);
        }
    }
}
