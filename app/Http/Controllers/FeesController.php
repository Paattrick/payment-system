<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Models\User;
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
            ->whereNotNull('meta')
            ->paginate($request->per_page);

        return Inertia::render('Admin/Fees/Index', [
            'fees' => FeeResource::collection($fees)
        ]);
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
            'meta' => $request->meta,
            'school_year' => $request->school_year,
            'name' => $validated['name']
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {
        $fee->update([
            'meta' => $request->fees
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
