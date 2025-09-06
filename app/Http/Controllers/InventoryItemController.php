<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::with('category');
        
        // Aplicar filtros
        if ($request->has('category') && $request->category) {
            $query->where('inventory_category_id', $request->category);
        }
        
        if ($request->has('stock_status')) {
            if ($request->stock_status == 'low') {
                $query->whereRaw('quantity <= minimum_stock');
            } elseif ($request->stock_status == 'normal') {
                $query->whereRaw('quantity > minimum_stock');
            }
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $items = $query->paginate(10);
        $categories = InventoryCategory::all();
        
        // Para el gráfico de resumen
        $categoryLabels = [];
        $categoryQuantities = [];
        
        $categorySummary = InventoryItem::select('inventory_category_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('inventory_category_id')
            ->with('category')
            ->get();
            
        foreach ($categorySummary as $summary) {
            $categoryLabels[] = $summary->category->name;
            $categoryQuantities[] = $summary->total_quantity;
        }
        
        // Ítems con stock bajo para el panel lateral
        $lowStockItems = InventoryItem::whereRaw('quantity <= minimum_stock')
            ->orderBy('quantity')
            ->take(5)
            ->get();
            
        $lowStockTotal = InventoryItem::whereRaw('quantity <= minimum_stock')->count();
        
        return view('inventory-items.index', compact('items', 'categories', 'categoryLabels', 'categoryQuantities', 'lowStockItems', 'lowStockTotal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = InventoryCategory::all();
        return view('inventory-items.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:inventory_items',
            'description' => 'nullable|string',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'minimum_stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-items.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        $item = InventoryItem::create($request->all());
        
        // Registrar movimiento inicial si hay stock
        if ($request->quantity > 0) {
            $this->registerStockMovement($item->id, 'add', $request->quantity, 'Stock inicial');
        }
        
        return redirect()->route('admin.inventory-items.index')
            ->with('success', 'Producto añadido correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = InventoryItem::with('category')->findOrFail($id);
        
        // Obtener el historial de movimientos
        $movements = DB::table('inventory_movements')
            ->where('inventory_item_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $lastMovement = DB::table('inventory_movements')
            ->where('inventory_item_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return view('inventory-items.show', compact('item', 'movements', 'lastMovement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = InventoryItem::findOrFail($id);
        $categories = InventoryCategory::all();
        
        return view('inventory-items.edit', compact('item', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = InventoryItem::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:inventory_items,code,' . $id,
            'description' => 'nullable|string',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'unit_price' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'minimum_stock' => 'required|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-items.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // No actualizamos la cantidad directamente, eso se maneja con los métodos add/remove stock
        $item->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'inventory_category_id' => $request->inventory_category_id,
            'unit_price' => $request->unit_price,
            'location' => $request->location,
            'minimum_stock' => $request->minimum_stock,
        ]);
        
        return redirect()->route('admin.inventory-items.show', $item->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = InventoryItem::findOrFail($id);
        
        try {
            $item->delete();
            return redirect()->route('admin.inventory-items.index')
                ->with('success', 'Producto eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('admin.inventory-items.index')
                ->with('error', 'No se pudo eliminar el producto. Verifique que no esté siendo usado en alguna otra parte del sistema.');
        }
    }
    
    /**
     * Add stock to the inventory item.
     */
    public function addStock(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'add_quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $quantity = $request->add_quantity;
        
        DB::beginTransaction();
        try {
            // Actualizar cantidad
            $item->quantity += $quantity;
            $item->save();
            
            // Registrar movimiento
            $this->registerStockMovement($item->id, 'add', $quantity, $request->notes);
            
            DB::commit();
            return redirect()->back()->with('success', "Se añadieron {$quantity} unidades al inventario.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el inventario.');
        }
    }
    
    /**
     * Remove stock from the inventory item.
     */
    public function removeStock(Request $request, $id)
    {
        $item = InventoryItem::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'remove_quantity' => 'required|integer|min:1|max:'.$item->quantity,
            'notes' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $quantity = $request->remove_quantity;
        
        DB::beginTransaction();
        try {
            // Actualizar cantidad
            $item->quantity -= $quantity;
            $item->save();
            
            // Registrar movimiento
            $this->registerStockMovement($item->id, 'remove', $quantity, $request->notes);
            
            DB::commit();
            return redirect()->back()->with('success', "Se retiraron {$quantity} unidades del inventario.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el inventario.');
        }
    }
    
    /**
     * Register a stock movement in the database.
     */
    private function registerStockMovement($itemId, $type, $quantity, $notes = null)
    {
        DB::table('inventory_movements')->insert([
            'inventory_item_id' => $itemId,
            'type' => $type,
            'quantity' => $quantity,
            'notes' => $notes,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Export inventory items.
     */
    public function export(Request $request)
    {
        $format = $request->format ?? 'pdf';
        
        // Aquí implementarías la lógica de exportación según el formato
        // Por ahora, simplemente redirigimos de vuelta con un mensaje
        return redirect()->route('admin.inventory-items.index')
            ->with('info', "Función de exportación en {$format} no disponible en esta versión.");
    }
    
    /**
     * Generate and print barcode for an item.
     */
    public function printBarcode($id)
    {
        $item = InventoryItem::findOrFail($id);
        
        // Aquí implementarías la lógica de generación de códigos de barras
        // Por ahora, simplemente redirigimos de vuelta con un mensaje
        return redirect()->route('admin.inventory-items.show', $id)
            ->with('info', "Función de impresión de código de barras no disponible en esta versión.");
    }
}
