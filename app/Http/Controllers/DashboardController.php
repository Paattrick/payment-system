<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Fee;
use App\Models\History;
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
        // dd($data);
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

        if($type == 'generateTotalCollectibles') {

            $csv = SimpleExcelWriter::streamDownload('totalCollectibles.csv');

                $row = [
                    'Total Collectibles' => $data[0],
                    $data[1] == 'daily' ? 'Date' : 'Selected Month' => $data[1] == 'daily' ? Carbon::parse($data[1])->toDateString() : Carbon::parse($data[2][0])->toDateString() .' - '. Carbon::parse($data[2][1])->toDateString(),
                    
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