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
    public function index()
    {
        //
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
        $school_year->update([
            'status' => 'active'
        ]);

        $data = SchoolYear::where('id', '!=', $school_year->id)
            ->update(['status' => 'inactive']);

        return response($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
