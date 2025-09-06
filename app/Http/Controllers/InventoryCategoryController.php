<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = InventoryCategory::withCount('inventoryItems')
                        ->orderBy('name')
                        ->paginate(10);
                        
        return view('inventory-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // No se usa, se gestiona mediante modal
        return redirect()->route('admin.inventory-categories.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:inventory_categories',
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-categories.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        InventoryCategory::create($request->all());
        
        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // No se usa, redirigimos al listado de inventario filtrado por esta categoría
        return redirect()->route('admin.inventory-items.index', ['category' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // No se usa, se gestiona mediante modal
        return redirect()->route('admin.inventory-categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = InventoryCategory::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $id,
            'description' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-categories.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        $category->update($request->all());
        
        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = InventoryCategory::findOrFail($id);
        
        // Verificar que no tenga ítems asociados
        if ($category->inventoryItems()->count() > 0) {
            return redirect()->route('admin.inventory-categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
