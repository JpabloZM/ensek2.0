<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::orderBy('name')
            ->withCount('serviceRequests')
            ->paginate(10);
        
        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name,NULL,id,deleted_at,NULL',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'duration' => 'required|integer|min:5',
            'special_requirements' => 'nullable|string',
            'materials_included' => 'nullable|string',
            'requires_technician_approval' => 'boolean',
            'active' => 'boolean',
        ], [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'name.unique' => 'Este nombre de servicio ya está en uso.',
            'description.required' => 'La descripción del servicio es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio no puede ser negativo.',
            'tax_rate.required' => 'La tasa de impuesto es obligatoria.',
            'tax_rate.numeric' => 'La tasa de impuesto debe ser un valor numérico.',
            'tax_rate.min' => 'La tasa de impuesto no puede ser negativa.',
            'tax_rate.max' => 'La tasa de impuesto no puede superar el 100%.',
            'duration.required' => 'La duración es obligatoria.',
            'duration.integer' => 'La duración debe ser un número entero.',
            'duration.min' => 'La duración mínima es de 5 minutos.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->tax_rate = $request->tax_rate;
        $service->duration = $request->duration;
        $service->special_requirements = $request->special_requirements;
        $service->materials_included = $request->materials_included;
        $service->requires_technician_approval = $request->has('requires_technician_approval');
        $service->active = $request->has('active');
        $service->save();

        return redirect()->route('admin.services.index')
            ->with('success', 'Servicio creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = Service::with('serviceRequests')->findOrFail($id);
        
        // Contar solicitudes por estado
        $pendientes = $service->serviceRequests->where('status', 'pendiente')->count();
        $agendadas = $service->serviceRequests->where('status', 'agendado')->count();
        $completadas = $service->serviceRequests->where('status', 'completado')->count();
        $canceladas = $service->serviceRequests->where('status', 'cancelado')->count();
        $total = $service->serviceRequests->count();
        
        return view('services.show', compact('service', 'pendientes', 'agendadas', 'completadas', 'canceladas', 'total'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $service = Service::findOrFail($id);
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:services,name,' . $id . ',id,deleted_at,NULL',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'duration' => 'required|integer|min:5',
            'special_requirements' => 'nullable|string',
            'materials_included' => 'nullable|string',
            'requires_technician_approval' => 'boolean',
            'active' => 'boolean',
        ], [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'name.unique' => 'Este nombre de servicio ya está en uso.',
            'description.required' => 'La descripción del servicio es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio no puede ser negativo.',
            'tax_rate.required' => 'La tasa de impuesto es obligatoria.',
            'tax_rate.numeric' => 'La tasa de impuesto debe ser un valor numérico.',
            'tax_rate.min' => 'La tasa de impuesto no puede ser negativa.',
            'tax_rate.max' => 'La tasa de impuesto no puede superar el 100%.',
            'duration.required' => 'La duración es obligatoria.',
            'duration.integer' => 'La duración debe ser un número entero.',
            'duration.min' => 'La duración mínima es de 5 minutos.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->tax_rate = $request->tax_rate;
        $service->duration = $request->duration;
        $service->special_requirements = $request->special_requirements;
        $service->materials_included = $request->materials_included;
        $service->requires_technician_approval = $request->has('requires_technician_approval');
        $service->active = $request->has('active');
        $service->save();

        return redirect()->route('admin.services.show', $service->id)
            ->with('success', 'Servicio actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service = Service::findOrFail($id);
        
        // Verificar si hay solicitudes pendientes o agendadas asociadas
        $activeRequestsCount = $service->serviceRequests()
                              ->whereIn('status', ['pendiente', 'agendado'])
                              ->count();
        
        if ($activeRequestsCount > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar este servicio porque tiene solicitudes pendientes o agendadas.');
        }
        
        // Usar soft delete
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Servicio eliminado correctamente');
    }
    
    /**
     * Mostrar los servicios eliminados (soft-deleted).
     */
    public function trashed()
    {
        $trashedServices = Service::onlyTrashed()
            ->orderBy('name')
            ->paginate(10);
            
        return view('services.trashed', compact('trashedServices'));
    }
    
    /**
     * Restaurar un servicio eliminado.
     */
    public function restore(string $id)
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        $service->restore();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Servicio restaurado correctamente');
    }
    
    /**
     * Eliminar permanentemente un servicio.
     */
    public function forceDelete(string $id)
    {
        $service = Service::onlyTrashed()->findOrFail($id);
        
        // Verificar si hay solicitudes asociadas en general
        $requestsCount = $service->serviceRequests()->count();
        
        if ($requestsCount > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar permanentemente este servicio porque tiene solicitudes asociadas en el historial.');
        }
        
        $service->forceDelete();
        
        return redirect()->route('admin.services.trashed')
            ->with('success', 'Servicio eliminado permanentemente');
    }
}
