<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Resources\StudentResource;
use Illuminate\Validation\Rule;

class StudentController extends Controller
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
            ->when($request->filled('grade'), fn ($query) => $query->where('grade', $request->grade))
            ->when($request->filled('section'), fn ($query) => $query->where('section', $request->section))
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
            })
            ->where('status', 'active')
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/Students/List/Index', [
            'students' => StudentResource::collection($students)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    private function validateRequest($request)
    {
        return $request->validate(
            [
                'last_name' => 'required|string',
                'email' => 'nullable|email',
                'middle_name' => 'required|string',
                'name' => 'required|string',
                'suffix_name' => 'nullable|string',
                'lrn' => 'required|string',
                'birthday' => 'required|string|date',
                'age' => 'required|string',
                'contact_number' => 'required|string',
                'gender' => 'required|string',
                'grade' => 'required|string',
                'section' => 'required|string',
                'province' => 'required|string',
                'municipality' => 'required|string',
                'barangay' => 'required|string',
                'id_number' => 'required|string',
                'password' => 'nullable|max:255|same:confirmation',
                'confirmation' => 'nullable|same:password',
            ],
            [
                'password.same' => 'Password does not match.',
                'confirmation.same' => 'Password does not match.'
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $encrypted_password = Hash::make($validated['id_number']);

        User::create(
            [
                'name' => $validated['name'],
                'middle_name' => $validated['middle_name'],
                'last_name' => $validated['last_name'],
                'suffix_name' => $validated['suffix_name'],
                'lrn' => $validated['lrn'],
                'birthday' => $validated['birthday'],
                'age' => $validated['age'],
                'contact_number' => $validated['contact_number'],
                'gender' => $validated['gender'],
                'grade' => $validated['grade'],
                'section' => $validated['section'],
                'province' => $validated['province'],
                'municipality' => $validated['municipality'],
                'barangay' => $validated['barangay'],
                'id_number' => $validated['id_number'],
                'password' => $encrypted_password,
                'email' => $validated['id_number'] . '@gnhs.edu.ph',
                'status' => 'active'
            ]
        )->assignRole('student');

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
    public function update(Request $request, User $student)
    {
        $validated = $this->validateRequest($request);

        $student->update($validated);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $student)
    {
    }

    public function archive(Request $request, User $student)
    {
        $student->update([
            'status' => "archived"
        ]);

        $student->save();

        return redirect()->back();
    }

    public function archiveRestore(Request $request, User $student)
    {
        $student->update([
            'status' => "active"
        ]);

        $student->save();

        return redirect()->back();
    }

    public function filterStudent()
    {
        return Inertia::render('Admin/Students/Dashboard/Index');
    }
}
