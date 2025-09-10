<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use Illuminate\Database\Seeder;

class UpdateStockLevelsSeeder extends Seeder
{
    /**
     * Actualiza los niveles de stock de algunos productos para que aparezcan como "stock bajo"
     */
    public function run(): void
    {
        // Actualizar productos a stock bajo (por código)
        $lowStockItems = [
            'TOOL-002' => [ // Multímetro Digital
                'quantity' => 2,
                'minimum_stock' => 5
            ],
            'PART-002' => [ // Motor Ventilador
                'quantity' => 1, 
                'minimum_stock' => 3
            ],
            'EQ-003' => [ // Calentador Solar
                'quantity' => 0,
                'minimum_stock' => 1
            ]
        ];
        
        foreach ($lowStockItems as $code => $data) {
            $item = InventoryItem::where('code', $code)->first();
            
            if ($item) {
                $item->update([
                    'quantity' => $data['quantity'],
                    'minimum_stock' => $data['minimum_stock']
                ]);
                
                $this->command->info("Producto {$item->name} actualizado a stock bajo: {$data['quantity']}/{$data['minimum_stock']}");
            } else {
                $this->command->error("Producto con código {$code} no encontrado");
            }
        }
        
        $this->command->info('Actualización de niveles de stock completada.');
    }
}
