<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SchoolYear;

class SchoolYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $school_years = SchoolYear::query()
            ->whereNotNull('name')
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/SchoolYears/Index', [
            'school_years' => $school_years
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
        
        $date = $request->date_from . ' - ' . $request->date_to;
        // $validated = $date->validate([
        //     'date' => 'required|unique:school_years,name'
        // ]);
        $data = SchoolYear::create([
            'name' => $date,
            'status' => 'inactive'
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
    public function update(Request $request, SchoolYear $school_year)
    {
        if($request->has('current_school_year')) {
            $school_year->update([
                'current_school_year' => $request->current_school_year,
                'name' => $request->name
            ]);
            
            SchoolYear::where('id', '!=', $request->id)
                ->update(['current_school_year' => 0]);

            return redirect()->back();

        } else {

            $school_year->update([
                'status' => 'active'
            ]);
    
            $data = SchoolYear::where('id', '!=', $school_year->id)
                ->update(['status' => 'inactive']);
    
            return response($data);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}