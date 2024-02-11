<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Resources\ArchiveResource;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $students = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'student'))
            // ->when($request->filled('name'), fn ($query) => $query->where('name', 'LIKE', "%{$request->name}%")->orWhere('last_name', 'LIKE', "%{$request->name}%"))
            // ->when($request->filled('grade'), fn ($query) => $query->where('grade', $request->grade))
            // ->when($request->filled('section'), fn ($query) => $query->where('section', $request->section))
            ->where('status', 'archived')
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/Archives/Index', [
            'archives' => ArchiveResource::collection($students)
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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
