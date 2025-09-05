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
            'name' => 'required|string|max:255|unique:services',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'active' => 'boolean',
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
        $service->duration = $request->duration;
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
            'name' => 'required|string|max:255|unique:services,name,' . $id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->duration = $request->duration;
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
        
        // Verificar si hay solicitudes asociadas
        $requestsCount = $service->serviceRequests()->count();
        
        if ($requestsCount > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar este servicio porque tiene solicitudes asociadas.');
        }
        
        $service->delete();
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Servicio eliminado correctamente');
    }
}
