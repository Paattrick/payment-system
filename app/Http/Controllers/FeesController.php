<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Http\Resources\FeeResource;

class FeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $fees = Fee::query()
            ->whereNotNull('name')
            ->paginate($request->per_page);

        return Inertia::render('Admin/Fees/Index', [
            'fees' => FeeResource::collection($fees)
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
            'name' => 'required|string|unique:fees,name'
        ]);

        Fee::create([
            'name' => $validated['name'],
            'meta' => $request->meta
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
    public function update(Request $request, Fee $fee)
    {
        $fee->update([
            'name' => $request->name,
            'meta' => $request->meta
        ]);

        $fee->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fee $fee)
    {
        $fee->delete();

        return redirect()->back();
    }
}
