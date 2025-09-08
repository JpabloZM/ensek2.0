<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skills = Skill::withCount('technicians')->paginate(10);
        return view('skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('skills.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);

        try {
            $skill = Skill::create($validated);
            return redirect()->route('admin.skills.index')
                ->with('success', 'Habilidad creada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear la habilidad: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $skill = Skill::with('technicians.user')->findOrFail($id);
        return view('skills.show', compact('skill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $skill = Skill::findOrFail($id);
        return view('skills.edit', compact('skill'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $skill = Skill::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:skills,name,' . $id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
        ]);

        try {
            $skill->update($validated);
            return redirect()->route('admin.skills.index')
                ->with('success', 'Habilidad actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar la habilidad: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $skill = Skill::findOrFail($id);
        
        try {
            // Using soft delete since the model uses SoftDeletes trait
            $skill->delete();
            return redirect()->route('admin.skills.index')
                ->with('success', 'Habilidad eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la habilidad: ' . $e->getMessage());
        }
    }
    
    /**
     * Return list of skills for API requests.
     */
    public function apiList()
    {
        $skills = Skill::select(['id', 'name', 'category'])->get();
        return response()->json($skills);
    }
}
