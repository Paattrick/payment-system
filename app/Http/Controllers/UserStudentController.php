<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Models\User;
use App\Models\History;
use App\Http\Resources\FeeResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UserStudentController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $fees = Fee::query()
            ->whereNotNull('meta')
            ->paginate($request->per_page);

        return Inertia::render('Student/Billings/Index', [
            'fees' => FeeResource::collection($fees)
        ]);
    }

    public function submitFees(Request $request, User $student)
    {
       
        
        if($request->hasFile('image')) {
            $validateFile = $request->validate([
                'reference' => 'required|unique:histories,reference',
                'image' => 'required'
            ]);

            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $path = 'public/images/' . $fileName;
            Storage::disk('local')->put($path, file_get_contents($file));

            $history = History::create([
                'student_id' => $student->id,
                'name' => $student->name,
                'meta' => $request->fees,
                'file' => $fileName,
                'status' => 'pending',
                'reference' => $validateFile['reference'],
                'mode_of_payment' => $request->type,
                'school_year_id' => intval($request->school_year_id)
            ]);

            $student->update([
                'student_fees' => $request->fees,
            ]);
        }

        if($request->type == "cash") {
            $validateFile = $request->validate([
                'reference' => 'required|unique:histories,reference',
            ]);
            $history = History::create([
                'student_id' => $student->id,
                'name' => $student->name,
                'meta' => $request->fees,
                'status' => 'pending',
                'reference' => $validateFile['reference'],
                'mode_of_payment' => $request->type,
                'school_year_id' => intval($request->school_year_id)
            ]);
            
            $student->update([
                'student_fees' => $request->fees,
            ]);
        }


        return redirect()->back();
    }

    public function submitPayment(Request $request, User $student)
    {
        $student->update([
            'student_fees' => $request->meta,
        ]);

        $history = History::where('id', $request->transactionId)
            ->update([
                'meta' => $request->meta,
                'status' => 'accepted',
                'collector_id' => $request->collector_id,
                'reference' =>  $request->type == 'cash' ? 'CASH-'. $request->transactionId : $request->reference,
                'school_year_id' => intval($request->school_year_id)
            ]);

        $student->save();

        return redirect()->back();
    }

    public function syncStudentFees(Request $request, User $student)
    {
        $student->update([
            'student_fees' => $request->meta,
        ]);

        $student->save();

        return redirect()->back();
    }

    public function declinePayment(Request $request, User $student)
    {
        $history = History::where('id', $request->transactionId)
            ->update([
                'status' => 'declined',
                'note' => $request->note,
                'reference' => $request->type == 'cash' ? 'CASH-'. $request->transactionId : $request->reference,
                'school_year_id' => intval($request->school_year_id),
                'collector_id' => $request->collector_id,
            ]);

        $student->update([
            'meta' => $request->meta
        ]);

        return redirect()->back();
    }

    public function payments(Request $request)
    {
        $fees = Fee::query()
            ->whereNotNull('name')
            ->get();

        return Inertia::render('Student/Billings/Payments/Index', [
            'selectedBillings' => $request->selectedBillings,
            'fees' => FeeResource::collection($fees)
        ]);
    }
}