<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Fee;
use App\Models\History;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Arr;
use Carbon\Carbon;

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
    
            $archivedStudents = User::query()
                ->where('status', 'archived')
                ->count();

            $activeSchoolYear = SchoolYear::query()
                ->where('status', 'active')
                ->first();

            $activeStudents = User::query()
                ->where('status', 'active')
                ->get();
            
            $totalCollected = 0;
            $total = 0;
            if($activeSchoolYear !== null)
            {
                
            
            $collectibles = Fee::query()
                ->where('school_year_id', $activeSchoolYear->id)
                ->get()
                ->toArray();

            $collectiblesCollection = collect($collectibles);
            
            foreach($collectiblesCollection as $val) {
                $total = $total + floatval($val['total_collectibles']);
            }

            $collectedCollectibles = History::query()
                    ->where('school_year_id',  $activeSchoolYear->id)
                    ->where('status', 'accepted')
                    ->get()
                    ->toArray();
    
                $collectedCollectiblesCollection = collect($collectedCollectibles);
                
                
                
                foreach($collectedCollectiblesCollection as $val) {
                    foreach($val['meta'] as $meta) {
                       foreach($meta['meta'] as $data) {
                           $totalCollected = $totalCollected + floatval($data['totalPaid']);
                       }
                    }
                }
            }
            $grades = Grade::query()
            ->whereNotNull('grade')
            ->orderBy('grade')
            ->get();
            
            return Inertia::render('Dashboard', [
                'students' => $user,
                'activeStudents' => $activeStudents,
                'archivedStudents' => $archivedStudents,
                'history' => $history,
                'totalCollectibles' => $total,
                'collectedCollectibles' => $totalCollected,
                'grades' => $grades 
            ]);
        }
    }

    public function export($data, $type)
    {
        if($type == 'generateTotalEnrolledStudents') {

            $csv = SimpleExcelWriter::streamDownload('students.csv');
    
            foreach ($data[0] as $file) {
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
            $csv->addRow([
                'Total Collectibles' => 'Total Collectibles: ' . $data[1]
            ]);
        }

        if($type == 'generateTotalCollectibles') {
            $csv = SimpleExcelWriter::streamDownload('totalCollectibles.csv');

                $row = [
                    'Total Collectibles' => $data[0],
                    $data[1] == 'daily' ? 'Date' : 'Selected Month' => $data[1] == 'daily' ? $data[2] : Carbon::parse($data[2][0])->toDateString() .' - '. Carbon::parse($data[2][1])->toDateString(),
                    
                ];
                // Arr::forget($row, $excludeColumn);
    
                $csv->addRow($row);
                
        }

        return $csv->toBrowser();
    }

    public function getExportType(Request $request) 
    {
        if($request->type == 'generateTotalEnrolledStudents') {
            $students = User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'student'))
                ->whereNotNull('name')
                ->when($request->input('grade'), function ($query, $filter) use ($request) {
                    $query->whereJsonContains('enrolled_grades', $request->grade);
                })
                ->when($request->input('section'), function ($query, $filter) use ($request) {
                    $query->whereJsonContains('enrolled_sections', $request->section);
                })
                ->when($request->input('status'), function ($query, $filter) use ($request) {
                    $query->where('status', $request->status);
                })
                ->get()
                ->toArray();
            
            $studentsCollection = collect($students);
            $total = 0;
                
            foreach($studentsCollection as $val) {
                if($val['student_fees'] !== null) {
                    foreach($val['student_fees'] as $meta) {
                        foreach($meta['meta'] as $data) {
                            if($data['totalPaid'] !== null) {
                                $total = $total + floatval($data['totalPaid']);
                            }
                        }
                    }
                }
            }

            $data = [$studentsCollection, $total];
            
            $this->export($data, $request->type);
        }

        if($request->type == 'generateTotalCollectibles') {

            if($request->collectiblesType == null) {
                $collectibles = Fee::query()
                    ->where('school_year_id', $request->school_year)
                    ->get()
                    ->toArray();
    
                $collectiblesCollection = collect($collectibles);
                $total = 0;
                
                foreach($collectiblesCollection as $val) {
                    $total = $total + floatval($val['total_collectibles']);
                }
            }
            
            if($request->collectiblesType != null) {
                $collectibles = History::query()
                    ->where('school_year_id', $request->school_year)
                    ->when($request->collectiblesType == 'daily', function ($query, $filter) use ($request) {
                        $query->whereDate('created_at', Carbon::parse($request->collectiblesDate)->toDateString());
                    })
                    ->when($request->collectiblesType == 'monthly', function ($query, $filter) use ($request) {
                        $query->whereBetween('created_at', [Carbon::parse($request->collectiblesDate[0])->toDateString(), Carbon::parse($request->collectiblesDate[1])->toDateString()]);
                    })
                    ->where('status', 'accepted')
                    ->get()
                    ->toArray();
    
                $collectiblesCollection = collect($collectibles);
                
                $total = 0;
                
                foreach($collectiblesCollection as $val) {
                    foreach($val['meta'] as $meta) {
                       foreach($meta['meta'] as $data) {
                           $total = $total + floatval($data['totalPaid']);
                       }
                    }
                }
            }

            $data = [$total, $request->collectiblesType, $request->collectiblesDate];
            
            $this->export($data, $request->type);
        }
    }
}