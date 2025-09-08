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
        // Categorías principales (sin parent_id)
        $rootCategories = InventoryCategory::withCount(['inventoryItems', 'children'])
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->paginate(10);
        
        // Categorías disponibles para ser parent (para el modal de creación)
        $availableParents = InventoryCategory::where('active', true)->orderBy('name')->get();
        
        return view('inventory-categories.index', compact('rootCategories', 'availableParents'));
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
            'name' => 'required|string|max:255|unique:inventory_categories,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:inventory_categories,id'
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.unique' => 'Ya existe una categoría con este nombre',
            'parent_id.exists' => 'La categoría padre seleccionada no es válida'
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-categories.index')
                ->withErrors($validator)
                ->withInput();
        }
        
        // Si hay parent_id, validar que no sea circular
        if ($request->parent_id) {
            if ($this->wouldCreateCircularReference($request->parent_id, null)) {
                return redirect()->route('admin.inventory-categories.index')
                    ->with('error', 'No se puede crear una referencia circular entre categorías.');
            }
        }
        
        InventoryCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'active' => $request->has('active')
        ]);
        
        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = InventoryCategory::with(['parent', 'children', 'inventoryItems'])->findOrFail($id);
        
        // Subproceso para cargar subcategorías recursivamente
        $this->loadChildrenRecursively($category->children);
        
        return view('inventory-categories.show', compact('category'));
    }

    /**
     * Cargar subcategorías recursivamente.
     */
    private function loadChildrenRecursively($children)
    {
        foreach ($children as $child) {
            $child->load(['children', 'inventoryItems']);
            $this->loadChildrenRecursively($child->children);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = InventoryCategory::findOrFail($id);
        $availableParents = InventoryCategory::getAvailableParents($id);
        
        return view('inventory-categories.edit', compact('category', 'availableParents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = InventoryCategory::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:inventory_categories,name,' . $id . ',id,deleted_at,NULL',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:inventory_categories,id'
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.unique' => 'Ya existe una categoría con este nombre',
            'parent_id.exists' => 'La categoría padre seleccionada no es válida'
        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.inventory-categories.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Si hay parent_id, validar que no sea el mismo ID y que no sea circular
        if ($request->parent_id) {
            if ($request->parent_id == $id) {
                return redirect()->route('admin.inventory-categories.edit', $id)
                    ->with('error', 'Una categoría no puede ser su propia categoría padre.');
            }
            
            if ($this->wouldCreateCircularReference($request->parent_id, $id)) {
                return redirect()->route('admin.inventory-categories.edit', $id)
                    ->with('error', 'No se puede crear una referencia circular entre categorías.');
            }
        }
        
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'active' => $request->has('active')
        ]);
        
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
        
        // Verificar que no tenga subcategorías
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.inventory-categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene subcategorías asociadas.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.inventory-categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
    
    /**
     * Display all trashed categories.
     */
    public function trashed()
    {
        $trashedCategories = InventoryCategory::onlyTrashed()
            ->orderBy('name')
            ->paginate(10);
            
        return view('inventory-categories.trashed', compact('trashedCategories'));
    }
    
    /**
     * Restore a soft-deleted category.
     */
    public function restore(string $id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);
        $category->restore();
        
        return redirect()->route('admin.inventory-categories.trashed')
            ->with('success', 'Categoría restaurada correctamente.');
    }
    
    /**
     * Force delete a category permanently.
     */
    public function forceDelete(string $id)
    {
        $category = InventoryCategory::onlyTrashed()->findOrFail($id);
        
        // Verificar nuevamente que no tenga elementos asociados
        if ($category->inventoryItems()->count() > 0 || $category->children()->count() > 0) {
            return redirect()->route('admin.inventory-categories.trashed')
                ->with('error', 'No se puede eliminar permanentemente la categoría porque tiene elementos o subcategorías asociadas.');
        }
        
        $category->forceDelete();
        
        return redirect()->route('admin.inventory-categories.trashed')
            ->with('success', 'Categoría eliminada permanentemente.');
    }
    
    /**
     * Verificar si crear una relación padre-hijo crearía una referencia circular.
     */
    private function wouldCreateCircularReference($parentId, $childId)
    {
        // Si estamos editando y el parent_id es null, no hay problema
        if ($parentId === null) {
            return false;
        }
        
        // Si el parent_id es el mismo que el childId, es circular
        if ($parentId == $childId) {
            return true;
        }
        
        // Inicializamos con el parent_id actual
        $currentParentId = $parentId;
        $visitedIds = [];
        
        // Recorremos hacia arriba la jerarquía para ver si llegamos al childId
        while ($currentParentId !== null) {
            // Evitar bucles infinitos
            if (in_array($currentParentId, $visitedIds)) {
                return true;
            }
            
            $visitedIds[] = $currentParentId;
            
            // Si encontramos el childId en la cadena de padres, es circular
            if ($currentParentId == $childId) {
                return true;
            }
            
            // Obtenemos el padre del padre actual
            $parent = InventoryCategory::find($currentParentId);
            if (!$parent) {
                break;
            }
            
            $currentParentId = $parent->parent_id;
        }
        
        return false;
    }
}
