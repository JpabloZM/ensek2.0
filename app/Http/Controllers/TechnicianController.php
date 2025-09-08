<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Technician;
use App\Models\TechnicianAvailability;
use App\Models\TechnicianTimeOff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'specialty_id' => 'nullable|exists:services,id',
            'title' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'certifications' => 'nullable|array',
            'employment_type' => 'nullable|string|in:full_time,part_time,contractor,on_call',
            'hire_date' => 'nullable|date',
            'years_experience' => 'nullable|integer|min:0',
            'profile_image' => 'nullable|image|max:2048',
            'skills' => 'nullable|array',
            'skills.*.id' => 'exists:skills,id',
            'skills.*.proficiency_level' => 'integer|min:1|max:5',
            'availabilities' => 'nullable|array',
            'availabilities.*.day_of_week' => 'required|integer|min:0|max:6',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.is_available' => 'boolean',
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

            // Procesar imagen de perfil si existe
            $profileImagePath = null;
            if ($request->hasFile('profile_image')) {
                $profileImagePath = $request->file('profile_image')->store('technicians', 'public');
            }

            // Crear técnico
            $technician = new Technician();
            $technician->user_id = $user->id;
            $technician->specialty_id = $validated['specialty_id'] ?? null;
            $technician->title = $validated['title'] ?? null;
            $technician->bio = $validated['bio'] ?? null;
            $technician->certifications = $validated['certifications'] ?? null;
            $technician->employment_type = $validated['employment_type'] ?? 'full_time';
            $technician->hire_date = $validated['hire_date'] ?? null;
            $technician->years_experience = $validated['years_experience'] ?? 0;
            $technician->profile_image = $profileImagePath;
            $technician->active = true;
            $technician->save();

            // Asignar habilidades
            if (isset($validated['skills']) && is_array($validated['skills'])) {
                foreach ($validated['skills'] as $skillData) {
                    $technician->skills()->attach($skillData['id'], [
                        'proficiency_level' => $skillData['proficiency_level'] ?? 3
                    ]);
                }
            }

            // Crear disponibilidad
            if (isset($validated['availabilities']) && is_array($validated['availabilities'])) {
                foreach ($validated['availabilities'] as $availabilityData) {
                    TechnicianAvailability::create([
                        'technician_id' => $technician->id,
                        'day_of_week' => $availabilityData['day_of_week'],
                        'start_time' => $availabilityData['start_time'],
                        'end_time' => $availabilityData['end_time'],
                        'is_available' => $availabilityData['is_available'] ?? true,
                    ]);
                }
            }
            // Si no se proporcionó disponibilidad, crear disponibilidad por defecto (Lunes-Viernes 9-17)
            else {
                for ($day = 1; $day <= 5; $day++) {
                    TechnicianAvailability::create([
                        'technician_id' => $technician->id,
                        'day_of_week' => $day,
                        'start_time' => '09:00',
                        'end_time' => '17:00',
                        'is_available' => true,
                    ]);
                }
            }

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
            'title' => 'nullable|string|max:100',
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
            $technician->specialty_id = $validated['specialty_id'] ?? null;
            $technician->title = $validated['title'] ?? null;
            $technician->employment_type = 'full_time';
            $technician->active = true;
            $technician->save();

            // Crear disponibilidad por defecto (Lun-Vie 9-17)
            for ($day = 1; $day <= 5; $day++) {
                TechnicianAvailability::create([
                    'technician_id' => $technician->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_available' => true,
                ]);
            }

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
                    'specialty' => $technician->specialty_id
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
            'specialty_id' => 'nullable|exists:services,id',
            'title' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'certifications' => 'nullable|array',
            'employment_type' => 'nullable|string|in:full_time,part_time,contractor,on_call',
            'hire_date' => 'nullable|date',
            'years_experience' => 'nullable|integer|min:0',
            'profile_image' => 'nullable|image|max:2048',
            'active' => 'boolean',
            'password' => 'nullable|string|min:8',
            'skills' => 'nullable|array',
            'skills.*.id' => 'exists:skills,id',
            'skills.*.proficiency_level' => 'integer|min:1|max:5',
            'availabilities' => 'nullable|array',
            'availabilities.*.id' => 'nullable|exists:technician_availabilities,id',
            'availabilities.*.day_of_week' => 'required|integer|min:0|max:6',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.is_available' => 'boolean',
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

            // Procesar imagen de perfil si existe
            if ($request->hasFile('profile_image')) {
                // Eliminar imagen anterior si existe
                if ($technician->profile_image) {
                    Storage::disk('public')->delete($technician->profile_image);
                }
                $profileImagePath = $request->file('profile_image')->store('technicians', 'public');
                $technician->profile_image = $profileImagePath;
            }

            // Actualizar técnico
            $technician->specialty_id = $validated['specialty_id'] ?? null;
            $technician->title = $validated['title'] ?? null;
            $technician->bio = $validated['bio'] ?? null;
            $technician->certifications = $validated['certifications'] ?? null;
            $technician->employment_type = $validated['employment_type'] ?? 'full_time';
            $technician->hire_date = $validated['hire_date'] ?? null;
            $technician->years_experience = $validated['years_experience'] ?? 0;
            $technician->active = $validated['active'] ?? true;
            $technician->save();

            // Actualizar habilidades
            if (isset($validated['skills'])) {
                // Eliminar todas las habilidades existentes
                $technician->skills()->detach();
                
                // Asignar nuevas habilidades
                foreach ($validated['skills'] as $skillData) {
                    $technician->skills()->attach($skillData['id'], [
                        'proficiency_level' => $skillData['proficiency_level'] ?? 3
                    ]);
                }
            }

            // Actualizar disponibilidad
            if (isset($validated['availabilities'])) {
                // Enfoque: eliminar todas las disponibilidades existentes y crear nuevas
                $technician->availabilities()->delete();
                
                foreach ($validated['availabilities'] as $availabilityData) {
                    TechnicianAvailability::create([
                        'technician_id' => $technician->id,
                        'day_of_week' => $availabilityData['day_of_week'],
                        'start_time' => $availabilityData['start_time'],
                        'end_time' => $availabilityData['end_time'],
                        'is_available' => $availabilityData['is_available'] ?? true,
                    ]);
                }
            }

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

    /**
     * Manage technician availability.
     */
    public function manageAvailability(string $id)
    {
        $technician = Technician::with(['user', 'availabilities'])->findOrFail($id);
        return view('technicians.availability', compact('technician'));
    }
    
    /**
     * Store technician availability.
     */
    public function storeAvailability(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);
        
        $validated = $request->validate([
            'availabilities' => 'required|array',
            'availabilities.*.day_of_week' => 'required|integer|min:0|max:6',
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.is_available' => 'boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Eliminar todas las disponibilidades existentes
            $technician->availabilities()->delete();
            
            // Crear nuevas disponibilidades
            foreach ($validated['availabilities'] as $availabilityData) {
                TechnicianAvailability::create([
                    'technician_id' => $technician->id,
                    'day_of_week' => $availabilityData['day_of_week'],
                    'start_time' => $availabilityData['start_time'],
                    'end_time' => $availabilityData['end_time'],
                    'is_available' => $availabilityData['is_available'] ?? true,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.technicians.show', $technician->id)
                ->with('success', 'Disponibilidad actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al actualizar la disponibilidad: ' . $e->getMessage());
        }
    }
    
    /**
     * Manage technician time off.
     */
    public function manageTimeOff(string $id)
    {
        $technician = Technician::with(['user', 'timeOffRequests'])->findOrFail($id);
        return view('technicians.timeoff', compact('technician'));
    }
    
    /**
     * Store technician time off.
     */
    public function storeTimeOff(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);
        
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:requested,approved,denied',
        ]);
        
        try {
            TechnicianTimeOff::create([
                'technician_id' => $technician->id,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);
            
            return redirect()->route('admin.technicians.time-off', $technician->id)
                ->with('success', 'Tiempo libre registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar tiempo libre: ' . $e->getMessage());
        }
    }
    
    /**
     * Update technician time off status.
     */
    public function updateTimeOffStatus(Request $request, string $id, string $timeOffId)
    {
        $technician = Technician::findOrFail($id);
        $timeOff = TechnicianTimeOff::where('technician_id', $technician->id)
                                    ->findOrFail($timeOffId);
        
        $validated = $request->validate([
            'status' => 'required|string|in:requested,approved,denied',
            'notes' => 'nullable|string',
        ]);
        
        try {
            $timeOff->status = $validated['status'];
            if (!empty($validated['notes'])) {
                $timeOff->notes = $validated['notes'];
            }
            $timeOff->save();
            
            return redirect()->route('admin.technicians.time-off', $technician->id)
                ->with('success', 'Estado de tiempo libre actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar estado de tiempo libre: ' . $e->getMessage());
        }
    }
    
    /**
     * Manage technician skills.
     */
    public function manageSkills(string $id)
    {
        $technician = Technician::with(['user', 'skills'])->findOrFail($id);
        $skills = Skill::all();
        return view('technicians.skills', compact('technician', 'skills'));
    }
    
    /**
     * Store technician skills.
     */
    public function storeSkills(Request $request, string $id)
    {
        $technician = Technician::findOrFail($id);
        
        $validated = $request->validate([
            'skills' => 'required|array',
            'skills.*.id' => 'required|exists:skills,id',
            'skills.*.proficiency_level' => 'required|integer|min:1|max:5',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Eliminar todas las habilidades existentes
            $technician->skills()->detach();
            
            // Asignar nuevas habilidades
            foreach ($validated['skills'] as $skillData) {
                $technician->skills()->attach($skillData['id'], [
                    'proficiency_level' => $skillData['proficiency_level']
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('admin.technicians.show', $technician->id)
                ->with('success', 'Habilidades actualizadas correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error al actualizar las habilidades: ' . $e->getMessage());
        }
    }
}
