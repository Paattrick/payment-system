<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Grade;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $grades = Grade::query()
            ->whereNotNull('grade')
            ->orderBy('grade')
            ->get();
       
        return Inertia::render('Admin/Grades/Index', [
            'grades' => $grades
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|unique:grades,grade',
            'sections' => 'required'
        ]);

        Grade::create([
            'grade' => $validated['grade'],
            'sections' => $validated['sections']
        ]); 

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'grade' => 'required|string|max:225|unique:grades,grade,'.$grade->id,
            'sections' => 'required'
        ]);

       $grade->update([
            'grade' => $validated['grade'],
            'sections' => $validated['sections']
        ]); 

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grade $grade)
    {
        $grade->delete();

        return redirect()->back();
    }
}
