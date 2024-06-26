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
use Illuminate\Support\Facades\Redis;
use League\Csv\Reader;
use League\Csv\Writer;

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
            ->when($request->filled('grade'), fn ($query) => $query->where('grade_id', intval($request->grade)))
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

        return Inertia::render('Admin/Students/Index', [
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
                'grade' => 'required',
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
                'grade_id' => $validated['grade'],
                'section' => $validated['section'],
                'address' => $validated['address'],
                'password' => $encrypted_password,
                'email' => $validated['lrn'] . '@gnhs.edu.ph',
                'status' => 'active',
                'active_school_year_id' => $request->school_year_id,
                'student_fees' => $validated['student_fees'],
                'enrolled_school_years' => [$request->current_school_year . ''],
                'enrolled_grades' => [$validated['section'] . ''],
                'enrolled_sections' => [$validated['grade'] . ''],
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

        $grade = $student->enrolled_grades;
        if(!in_array($request->grade, $grade)) {
            array_push($grade, $request->grade);
        }

        $section = $student->enrolled_sections;
        if(!in_array($request->section, $section)) {
            array_push($section, $request->section);
        }
        
        $student->update([
            'grade_id' => $request->grade,
            'enrolled_grades' => $grade,
            'enrolled_sections' => $section,
        ]);

        return redirect()->back();
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

    public function enrollStudents(Request $request) 
    {
        foreach($request->students as $key => $id) {
            $studentSchoolYears = User::find($id);
            $student = null;
            if($studentSchoolYears->enrolled_school_years == null) {
                $student = [$request->current_school_year];
            } else {
                $student = $studentSchoolYears->enrolled_school_years;
                if(!in_array($request->current_school_year, $student)) {
                    array_push($student, $request->current_school_year);
                }
            }
            User::where('id', $id)->update([
                'active_school_year_id' => $request->current_school_year,
                'enrolled_school_years' => $student
            ]);
        }
        return redirect()->back();
    }

    public function importCsv(Request $request)
    {
        $csv = Reader::createFromPath($request->file->getRealPath());
        $csv->setHeaderOffset(0);
       
        foreach($csv as $record)
        {
            User::create(
                [
                    'name' => $record['name'] == "" ? null : $record['name'],
                    'middle_name' => $record['middle_name'] == "" ? null : $record['middle_name'],
                    'last_name' => $record['last_name'] == "" ? null : $record['last_name'],
                    'suffix_name' => $record['suffix_name'] == "" ? null : $record['suffix_name'],
                    'lrn' => $record['lrn'] == "" ? null : $record['lrn'],
                    'contact_number' => $record['contact_number'] == "" ? null : $record['contact_number'],
                    'gender' => $record['gender'] == "" ? null : $record['gender'],
                    'address' => $record['address'] == "" ? null : $record['address'],
                    'password' => Hash::make($record['lrn']),
                    'email' => $record['lrn'] . '@gnhs.edu.ph',
                    'status' => 'active',
                    'active_school_year_id' => intval($request->school_year_id),
                    'enrolled_school_years' => [$request->current_school_year . ''],
                    'enrolled_grades' => [],
                    'enrolled_sections' => []
                ]
            )->assignRole('student');
        }

        return redirect()->back();
    }
}