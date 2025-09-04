<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $technicians = Technician::with(['user', 'specialtyService'])->paginate(10);
        return view('technicians.index', compact('technicians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('technicians.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:100',
            'skills' => 'nullable|string',
            'availability' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Obtener el rol de técnico
            $technicianRole = Role::where('name', 'technician')->first();

            // Crear usuario
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->phone = $validated['phone'] ?? null;
            $user->role_id = $technicianRole->id;
            $user->save();

            // Crear técnico
            $technician = new Technician();
            $technician->user_id = $user->id;
            $technician->specialty = $validated['specialty'] ?? null;
            $technician->skills = $validated['skills'] ?? null;
            $technician->availability = $validated['availability'] ?? 'Lunes a Viernes 9am-5pm';
            $technician->active = true;
            $technician->save();

            DB::commit();

            return redirect()->route('admin.technicians.index')
                ->with('success', 'Técnico creado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al crear el técnico: ' . $e->getMessage());
        }
    }
    
    /**
     * Quick add a new technician from the calendar view.
     */
    public function quickAdd(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'specialty_id' => 'nullable|exists:services,id',
            'skills' => 'nullable|string|max:500',
            'availability' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Generar contraseña temporal
            $tempPassword = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            
            // Obtener el rol de técnico
            $technicianRole = Role::where('name', 'technician')->first();
            
            if (!$technicianRole) {
                throw new \Exception('El rol de técnico no existe en el sistema');
            }

            // Crear usuario
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($tempPassword);
            $user->phone = $validated['phone'] ?? null;
            $user->role_id = $technicianRole->id;
            $user->save();

            // Crear técnico
            $technician = new Technician();
            $technician->user_id = $user->id;
            $technician->specialty = $validated['specialty_id'] ?? null;
            $technician->skills = $validated['skills'] ?? null;
            $technician->availability = $validated['availability'] ?? 'Lunes a Viernes 9am-5pm';
            $technician->active = true;
            $technician->save();

            DB::commit();

            // Registrar en el log la creación del técnico
            \Illuminate\Support\Facades\Log::info("Técnico creado desde el calendario: {$user->name} (ID: {$technician->id})");

            // Devolver respuesta JSON para actualización del calendario
            return response()->json([
                'success' => true,
                'technician' => [
                    'id' => $technician->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'specialty' => $technician->specialty
                ],
                'message' => 'Técnico agregado correctamente. Contraseña temporal: ' . $tempPassword,
                'password' => $tempPassword // Contraseña temporal para mostrarla al usuario
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error("Error al crear técnico: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el técnico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $technician = Technician::with(['user', 'specialtyService', 'schedules.serviceRequest.service'])->findOrFail($id);
        return view('technicians.show', compact('technician'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $technician = Technician::with(['user', 'specialtyService'])->findOrFail($id);
        return view('technicians.edit', compact('technician'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $technician->user_id,
            'phone' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:100',
            'skills' => 'nullable|string',
            'availability' => 'nullable|string',
            'active' => 'boolean',
            'password' => 'nullable|string|min:8',
        ]);

        DB::beginTransaction();

        try {
            // Actualizar usuario
            $user = $technician->user;
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'] ?? null;
            
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            
            $user->save();

            // Actualizar técnico
            $technician->specialty = $validated['specialty'] ?? null;
            $technician->skills = $validated['skills'] ?? null;
            $technician->availability = $validated['availability'] ?? 'Lunes a Viernes 9am-5pm';
            $technician->active = $validated['active'] ?? true;
            $technician->save();

            DB::commit();

            return redirect()->route('admin.technicians.show', $technician->id)
                ->with('success', 'Técnico actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al actualizar el técnico: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $technician = Technician::findOrFail($id);

        // En lugar de eliminar, desactivar el técnico
        $technician->active = false;
        $technician->save();

        return redirect()->route('admin.technicians.index')
            ->with('success', 'Técnico desactivado correctamente.');
    }
}
