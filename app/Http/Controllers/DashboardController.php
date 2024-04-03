<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\History;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Arr;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
       
        if($authUser != null && $authUser->status == 'archived') {
            Auth::logout();
            return Redirect::route('login');
        } else {
            $request->merge([
                'per_page' => $request->per_page ?: '15',
            ]);
    
            $history = History::query()
                ->whereNotNull('student_id')
                ->where('status', 'pending')
                ->first();
    
            $user = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'student'))
                ->whereNotNull('name')
                ->latest()
                ->paginate($request->per_page);
    
            $activeStudents = User::query()
                ->where('status', 'active')
                ->count();
    
            $archivedStudents = User::query()
                ->where('status', 'archived')
                ->count();
            
            return Inertia::render('Dashboard', [
                'students' => $user,
                'activeStudents' => $activeStudents,
                'archivedStudents' => $archivedStudents,
                'history' => $history
            ]);
        }
    }

    public function export($data, $type)
    {
        if($type == 'generateTotalEnrolledStudents') {

            $csv = SimpleExcelWriter::streamDownload('students.csv');
    
            foreach ($data as $file) {
                $row = [
                    'Name' => data_get($file, 'name'),
                    'ID Number' => data_get($file, 'id_number'),
                    'Grade' => data_get($file, 'grade'),
                    'Section' => data_get($file, 'section'),
                    'Status' => data_get($file, 'status'),
                ];
    
                // Arr::forget($row, $excludeColumn);
    
                $csv->addRow($row);
            }
        }

        return $csv->toBrowser();
    }

    public function getExportType(Request $request) {
        if($request->type == 'generateTotalEnrolledStudents') {

            $students = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'student'))
                ->whereNotNull('name')
                ->when($request->input('grade'), function ($query, $filter) use ($request) {
                    $query->where('grade', $request->grade);
                })
                ->when($request->input('section'), function ($query, $filter) use ($request) {
                    $query->where('section', $request->section);
                })
                ->when($request->input('status'), function ($query, $filter) use ($request) {
                    $query->where('status', $request->status);
                })
                ->orderBy('grade')
                ->get();

            $studentsCollection = collect($students);
            
            $this->export($studentsCollection, $request->type);
            // foreach ($studentsCollection as $data) {
            //     dd(data_get($data, 'name'));
            // }
        }
    }
}
