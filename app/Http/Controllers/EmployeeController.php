<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $employees = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'employee'))
            ->whereNot('name', 'admin')
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/Employees/Index', [
            'employees' => EmployeeResource::collection($employees)
        ]);
    }

    private function validateRequest($request)
    {
        return $request->validate(
            [
                'last_name' => 'required|string',
                'email' => 'nullable|email',
                'middle_name' => 'nullable|string',
                'name' => 'required|string',
                'suffix_name' => 'nullable|string',
                'birthday' => 'required|string|date',
                'contact_number' => 'required|numeric|digits:11',
                'gender' => 'required|string',
                'address' => 'required|array',
                'address.province' => 'required|string',
                'address.municipality' => 'required|string',
                'address.barangay' => 'required|string',
                'lrn' => 'required|string',
            ],
            [
                'address.province.required' => 'The province field is required.',
                'address.municipality.required' => 'The municipality field is required.',
                'address.barangay.required' => 'The barangay field is required.',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $encrypted_password = Hash::make($validated['lrn']);

        User::create(
            [
                'name' => $validated['name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'suffix_name' => $validated['suffix_name'],
                'birthday' => $validated['birthday'],
                'contact_number' => $validated['contact_number'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'lrn' => $validated['lrn'],
                'password' => $encrypted_password,
                'email' => $validated['lrn'] . '@gnhs.edu.ph'
            ]
        )->assignRole('employee');

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
