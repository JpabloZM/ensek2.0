<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las categorías existentes
        $categories = InventoryCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->info('No hay categorías disponibles. Ejecuta primero el seeder de categorías.');
            return;
        }

        // Datos para productos de Herramientas
        $toolsCategory = $categories->where('name', 'Herramientas')->first();
        if ($toolsCategory) {
            $tools = [
                [
                    'name' => 'Destornillador Phillips',
                    'code' => 'TOOL-001',
                    'description' => 'Destornillador Phillips de precisión para trabajos eléctricos',
                    'quantity' => 25,
                    'unit_price' => 12.50,
                    'location' => 'Estantería A-1',
                    'minimum_stock' => 10,
                ],
                [
                    'name' => 'Multímetro Digital',
                    'code' => 'TOOL-002',
                    'description' => 'Multímetro digital para mediciones eléctricas',
                    'quantity' => 8,
                    'unit_price' => 89.99,
                    'location' => 'Estantería A-2',
                    'minimum_stock' => 5,
                ],
                [
                    'name' => 'Llave Ajustable',
                    'code' => 'TOOL-003',
                    'description' => 'Llave ajustable de 8 pulgadas',
                    'quantity' => 15,
                    'unit_price' => 18.75,
                    'location' => 'Estantería A-3',
                    'minimum_stock' => 8,
                ],
            ];
            
            foreach ($tools as $item) {
                $this->createInventoryItem($item, $toolsCategory->id);
            }
        }
        
        // Datos para productos de Repuestos
        $partsCategory = $categories->where('name', 'Repuestos')->first();
        if ($partsCategory) {
            $parts = [
                [
                    'name' => 'Termostato para Refrigerador',
                    'code' => 'PART-001',
                    'description' => 'Termostato compatible con refrigeradores modelo XYZ',
                    'quantity' => 12,
                    'unit_price' => 45.99,
                    'location' => 'Estantería B-1',
                    'minimum_stock' => 5,
                ],
                [
                    'name' => 'Motor Ventilador',
                    'code' => 'PART-002',
                    'description' => 'Motor para ventilador de condensador de aire acondicionado',
                    'quantity' => 4,
                    'unit_price' => 125.50,
                    'location' => 'Estantería B-2',
                    'minimum_stock' => 3,
                ],
                [
                    'name' => 'Capacitor de Arranque',
                    'code' => 'PART-003',
                    'description' => 'Capacitor de arranque para motores eléctricos',
                    'quantity' => 20,
                    'unit_price' => 15.25,
                    'location' => 'Estantería B-3',
                    'minimum_stock' => 10,
                ],
            ];
            
            foreach ($parts as $item) {
                $this->createInventoryItem($item, $partsCategory->id);
            }
        }
        
        // Datos para productos de Materiales
        $materialsCategory = $categories->where('name', 'Materiales')->first();
        if ($materialsCategory) {
            $materials = [
                [
                    'name' => 'Cable Eléctrico 12 AWG',
                    'code' => 'MAT-001',
                    'description' => 'Cable eléctrico de cobre calibre 12 AWG',
                    'quantity' => 500,
                    'unit_price' => 1.25,
                    'location' => 'Estantería C-1',
                    'minimum_stock' => 100,
                ],
                [
                    'name' => 'Tubería PVC 1/2"',
                    'code' => 'MAT-002',
                    'description' => 'Tubería PVC de 1/2 pulgada para instalaciones hidráulicas',
                    'quantity' => 300,
                    'unit_price' => 2.50,
                    'location' => 'Estantería C-2',
                    'minimum_stock' => 50,
                ],
                [
                    'name' => 'Cinta Aislante',
                    'code' => 'MAT-003',
                    'description' => 'Cinta aislante eléctrica de alta calidad',
                    'quantity' => 40,
                    'unit_price' => 3.99,
                    'location' => 'Estantería C-3',
                    'minimum_stock' => 20,
                ],
            ];
            
            foreach ($materials as $item) {
                $this->createInventoryItem($item, $materialsCategory->id);
            }
        }
        
        // Datos para productos de Equipos
        $equipmentCategory = $categories->where('name', 'Equipos')->first();
        if ($equipmentCategory) {
            $equipment = [
                [
                    'name' => 'Aire Acondicionado Split 12000 BTU',
                    'code' => 'EQ-001',
                    'description' => 'Aire acondicionado tipo split de 12000 BTU eficiencia energética A',
                    'quantity' => 5,
                    'unit_price' => 450.00,
                    'location' => 'Almacén D-1',
                    'minimum_stock' => 2,
                ],
                [
                    'name' => 'Bomba de Agua 1 HP',
                    'code' => 'EQ-002',
                    'description' => 'Bomba de agua potable de 1HP para uso residencial',
                    'quantity' => 3,
                    'unit_price' => 275.50,
                    'location' => 'Almacén D-2',
                    'minimum_stock' => 2,
                ],
                [
                    'name' => 'Calentador Solar 150L',
                    'code' => 'EQ-003',
                    'description' => 'Calentador solar de agua de 150 litros con tubos de vacío',
                    'quantity' => 2,
                    'unit_price' => 789.99,
                    'location' => 'Almacén D-3',
                    'minimum_stock' => 1,
                ],
            ];
            
            foreach ($equipment as $item) {
                $this->createInventoryItem($item, $equipmentCategory->id);
            }
        }
        
        // Datos para productos de Accesorios
        $accessoriesCategory = $categories->where('name', 'Accesorios')->first();
        if ($accessoriesCategory) {
            $accessories = [
                [
                    'name' => 'Control Remoto Universal',
                    'code' => 'ACC-001',
                    'description' => 'Control remoto universal para aires acondicionados',
                    'quantity' => 15,
                    'unit_price' => 25.99,
                    'location' => 'Estantería E-1',
                    'minimum_stock' => 5,
                ],
                [
                    'name' => 'Filtro de Agua',
                    'code' => 'ACC-002',
                    'description' => 'Filtro de agua para purificador residencial',
                    'quantity' => 18,
                    'unit_price' => 35.50,
                    'location' => 'Estantería E-2',
                    'minimum_stock' => 8,
                ],
                [
                    'name' => 'Válvula Reguladora',
                    'code' => 'ACC-003',
                    'description' => 'Válvula reguladora de presión para sistemas de agua',
                    'quantity' => 6,
                    'unit_price' => 45.75,
                    'location' => 'Estantería E-3',
                    'minimum_stock' => 4,
                ],
            ];
            
            foreach ($accessories as $item) {
                $this->createInventoryItem($item, $accessoriesCategory->id);
            }
        }
        
        $this->command->info('Se han creado 15 productos de inventario en diferentes categorías.');
    }

    /**
     * Helper para crear un item de inventario
     */
    private function createInventoryItem($data, $categoryId)
    {
        $data['inventory_category_id'] = $categoryId;
        InventoryItem::create($data);
    }
}
