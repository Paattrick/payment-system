<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Models\User;
use App\Models\SchoolYear;
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

        $school_years = SchoolYear::query()
            ->whereNotNull('name')
            ->get();

        return Inertia::render('Admin/Fees/Index', [
            'fees' => FeeResource::collection($fees),
            'school_years' => $school_years
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:fees,name',
            'school_year' => 'required|integer',
            'meta' => 'required|array',
            'meta.*.clearance' => 'required|string|distinct',
            'meta.*.amount' => 'required|decimal:0,2',
            'meta.*.toPay' => 'required|decimal:0,2',
            'meta.*.balance' => 'required|decimal:0,2',
            'meta.*.totalPaid' => 'required|decimal:0,2',
        ], [
            'meta.*.clearance.distinct' => 'Duplicate value.',
            'meta.*.amount.decimal' => 'Amount must be decimal.',
            'meta.*.toPay' => 'Amount to pay must be decimal.',
            'meta.*.balance' => 'Balance must be decimal.',
            'meta.*.totalPaid' => 'required|decimal:0,2',
        ]);
        
        Fee::create([
            'meta' => $validated['meta'],
            'school_year_id' => $validated['school_year'],
            'name' => $validated['name'],
            'total_collectibles' => $request->collectibles
        ]);

        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fee $fee)
    {

        $validated = $request->validate([
            'name' => 'required|string|unique:fees,name,'. $fee->id,
            'school_year' => 'required|integer',
            'meta' => 'required|array',
            'meta.*.clearance' => 'required|string|distinct',
            'meta.*.amount' => 'required|decimal:0,2',
            'meta.*.toPay' => 'required|decimal:0,2',
            'meta.*.balance' => 'required|decimal:0,2',
        ], [
            'meta.*.clearance.distinct' => 'Duplicate value.',
            'meta.*.amount.decimal' => 'Amount must be decimal.',
            'meta.*.toPay' => 'Amount to pay must be decimal.',
            'meta.*.balance' => 'Balance must be decimal.',
        ]);

        $fee->update([
            'meta' => $validated['meta'],
            'school_year_id' => $validated['school_year'],
            'name' => $validated['name'],
            'total_collectibles' => $request->collectibles
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