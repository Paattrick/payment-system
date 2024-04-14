<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Resources\StudentResource;
use Illuminate\Validation\Rule;
use App\Models\Fee;
use App\Models\Grade;
use App\Http\Resources\FeeResource;

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

        $fees = Fee::query()
            ->whereNotNull('meta')
            ->get();

        $grades = Grade::query()
            ->whereNotNull('grade')
            ->orderBy('grade')
            ->get();

        return Inertia::render('Admin/Students/List/Index', [
            'students' => StudentResource::collection($students),
            'fees' => FeeResource::collection($fees),
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

    private function validateRequest($request)
    {
        return $request->validate(
            [
                'last_name' => 'required|string',
                'email' => 'nullable|email',
                'middle_name' => 'nullable|string',
                'name' => 'required|string',
                'suffix_name' => 'nullable|string',
                'lrn' => 'required|numeric|digits:11',
                'birthday' => 'required|string|date',
                'contact_number' => 'required|numeric|digits:11',
                'gender' => 'required|string',
                'grade' => 'required|string',
                'section' => 'required|string',
                'address' => 'required|array',
                'address.province' => 'required|string',
                'address.municipality' => 'required|string',
                'address.barangay' => 'required|string',
                'password' => 'nullable|max:255|same:confirmation',
                'confirmation' => 'nullable|same:password',
                'student_fees' => 'nullable'
            ],
            [
                'password.same' => 'Password does not match.',
                'confirmation.same' => 'Password does not match.',
                'contact_number.size' =>  'The contact number field must be 11 digits.',
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
                'lrn' => $validated['lrn'],
                'birthday' => $validated['birthday'],
                'contact_number' => $validated['contact_number'],
                'gender' => $validated['gender'],
                'grade' => $validated['grade'],
                'section' => $validated['section'],
                'address' => $validated['address'],
                'password' => $encrypted_password,
                'email' => $validated['lrn'] . '@gnhs.edu.ph',
                'status' => 'active',
                'active_school_year_id' => $request->school_year_id,
                'student_fees' => $validated['student_fees'],
            ]
        )->assignRole('student');

        return redirect()->back();
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
