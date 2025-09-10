<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventoryMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtros
        $filters = [
            'movement_type' => $request->input('movement_type', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
            'item_id' => $request->input('item_id', ''),
            'reference_type' => $request->input('reference_type', '')
        ];
        
        // Consulta base
        $query = InventoryMovement::with(['item', 'user']);
        
        // Aplicar filtros
        if ($filters['movement_type']) {
            $query->where('movement_type', $filters['movement_type']);
        }
        
        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if ($filters['item_id']) {
            $query->where('inventory_item_id', $filters['item_id']);
        }
        
        if ($filters['reference_type']) {
            $query->where('reference_type', $filters['reference_type']);
        }
        
        // Obtener movimientos con paginación
        $movements = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Datos para los filtros en la vista
        $items = InventoryItem::orderBy('name')->get();
        
        // Estadísticas resumen
        $stats = [
            'total_entries' => InventoryMovement::entries()->count(),
            'total_exits' => InventoryMovement::exits()->count(),
            'total_adjustments' => InventoryMovement::adjustments()->count(),
            'total_value' => InventoryItem::sum(DB::raw('quantity * unit_price')),
            'recent_movements' => InventoryMovement::with('item')->orderBy('created_at', 'desc')->limit(5)->get()
        ];
        
        return view('inventory-movements.index', compact('movements', 'items', 'filters', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = InventoryItem::where('is_active', true)->orderBy('name')->get();
        $providers = Provider::orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        
        return view('inventory-movements.create', compact('items', 'providers', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'movement_type' => ['required', Rule::in(['entry', 'exit', 'adjustment'])],
            'quantity' => 'required|integer|min:1',
            'reference_type' => ['required', Rule::in(['manual', 'provider', 'service', 'purchase'])],
            'reference_id' => 'required_unless:reference_type,manual',
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Iniciar una transacción de base de datos
        DB::beginTransaction();

        try {
            // Obtener el ítem de inventario
            $item = InventoryItem::findOrFail($request->inventory_item_id);
            
            // Calcular la cantidad anterior y nueva
            $previousQuantity = $item->quantity;
            $movementQuantity = $request->quantity;
            
            // Actualizar la cantidad en el inventario según el tipo de movimiento
            switch ($request->movement_type) {
                case 'entry':
                    $item->quantity += $movementQuantity;
                    break;
                case 'exit':
                    if ($item->quantity < $movementQuantity) {
                        return redirect()->back()
                            ->with('error', 'No hay suficiente stock disponible para esta salida.')
                            ->withInput();
                    }
                    $item->quantity -= $movementQuantity;
                    break;
                case 'adjustment':
                    // Para ajustes, se establece directamente la nueva cantidad
                    $item->quantity = $movementQuantity;
                    break;
            }
            
            // Guardar el cambio en el inventario
            $item->save();
            
            // Determinar el precio unitario
            $unitPrice = $request->unit_price ?? $item->unit_price;
            
            // Si es una entrada y tiene precio, actualizar el precio del ítem
            if ($request->movement_type === 'entry' && $request->unit_price) {
                $item->unit_price = $request->unit_price;
                $item->last_purchase_price = $request->unit_price;
                $item->last_purchase_date = now();
                $item->save();
            }
            
            // Crear el registro de movimiento
            $movement = new InventoryMovement([
                'inventory_item_id' => $request->inventory_item_id,
                'movement_type' => $request->movement_type,
                'quantity' => $movementQuantity,
                'reference_type' => $request->reference_type,
                'reference_id' => $request->reference_id !== 'manual' ? $request->reference_id : null,
                'notes' => $request->notes,
                'user_id' => Auth::id(),
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $item->quantity,
                'unit_price' => $unitPrice,
            ]);
            
            $movement->save();
            
            DB::commit();
            
            return redirect()->route('inventory-movements.index')
                ->with('success', 'Movimiento de inventario registrado correctamente.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al registrar el movimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movement = InventoryMovement::with(['item', 'user'])->findOrFail($id);
        
        // Cargar la referencia según el tipo
        if ($movement->reference_type && $movement->reference_id) {
            $movement->load('reference');
        }
        
        return view('inventory-movements.show', compact('movement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $movement = InventoryMovement::findOrFail($id);
        
        // Los movimientos no deberían editarse directamente, ya que afectan el inventario
        // Por lo general, debería hacerse un ajuste en lugar de editar
        return redirect()->route('inventory-movements.index')
            ->with('warning', 'Los movimientos no pueden ser editados directamente. Realice un ajuste si es necesario.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Los movimientos no deberían actualizarse directamente
        return redirect()->route('inventory-movements.index')
            ->with('warning', 'Los movimientos no pueden ser actualizados directamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Los movimientos no deberían eliminarse, ya que son un registro histórico
        return redirect()->route('inventory-movements.index')
            ->with('warning', 'Los movimientos no pueden ser eliminados por razones de auditoría.');
    }
    
    /**
     * Generate a report of inventory movements.
     */
    public function report(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'movement_type' => 'nullable|in:entry,exit,adjustment,all',
            'item_id' => 'nullable|exists:inventory_items,id',
            'report_type' => 'required|in:summary,detail',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Construir la consulta base
        $query = InventoryMovement::with(['item', 'user'])
            ->whereDate('created_at', '>=', $request->date_from)
            ->whereDate('created_at', '<=', $request->date_to);
            
        // Aplicar filtros adicionales
        if ($request->movement_type && $request->movement_type !== 'all') {
            $query->where('movement_type', $request->movement_type);
        }
        
        if ($request->item_id) {
            $query->where('inventory_item_id', $request->item_id);
        }
        
        // Obtener los resultados según el tipo de reporte
        if ($request->report_type === 'summary') {
            // Reporte resumido por tipo de movimiento
            $results = $query->select(
                'movement_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * unit_price) as total_value')
            )
            ->groupBy('movement_type')
            ->get();
            
            return view('inventory-movements.reports.summary', [
                'results' => $results,
                'dateFrom' => Carbon::parse($request->date_from)->format('d/m/Y'),
                'dateTo' => Carbon::parse($request->date_to)->format('d/m/Y'),
                'filters' => $request->all()
            ]);
        } else {
            // Reporte detallado
            $movements = $query->orderBy('created_at', 'desc')->get();
            
            return view('inventory-movements.reports.detail', [
                'movements' => $movements,
                'dateFrom' => Carbon::parse($request->date_from)->format('d/m/Y'),
                'dateTo' => Carbon::parse($request->date_to)->format('d/m/Y'),
                'filters' => $request->all()
            ]);
        }
    }
    
    /**
     * Show the form for generating reports.
     */
    public function reportForm()
    {
        $items = InventoryItem::orderBy('name')->get();
        
        return view('inventory-movements.reports.form', compact('items'));
    }
    
    /**
     * Show the inventory valuation report.
     */
    public function valuation()
    {
        // Obtener todas las categorías de inventario con sus items
        $categories = DB::table('inventory_categories')
            ->select(
                'inventory_categories.id',
                'inventory_categories.name',
                DB::raw('COUNT(inventory_items.id) as item_count'),
                DB::raw('SUM(inventory_items.quantity) as total_quantity'),
                DB::raw('SUM(inventory_items.quantity * inventory_items.unit_price) as total_value')
            )
            ->leftJoin('inventory_items', 'inventory_categories.id', '=', 'inventory_items.inventory_category_id')
            ->groupBy('inventory_categories.id', 'inventory_categories.name')
            ->get();
            
        // Obtener estadísticas generales de inventario
        $stats = [
            'total_items' => InventoryItem::count(),
            'total_quantity' => InventoryItem::sum('quantity'),
            'total_value' => InventoryItem::sum(DB::raw('quantity * unit_price')),
            'low_stock_count' => InventoryItem::whereRaw('quantity <= minimum_stock')->count()
        ];
        
        // Obtener los 10 items más valiosos
        $topItems = InventoryItem::orderByRaw('quantity * unit_price DESC')
            ->with('category')
            ->limit(10)
            ->get();
            
        return view('inventory-movements.reports.valuation', compact('categories', 'stats', 'topItems'));
    }
}
