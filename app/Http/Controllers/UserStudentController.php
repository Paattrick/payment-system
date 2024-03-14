<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Models\User;
use App\Models\History;
use App\Http\Resources\FeeResource;
use Illuminate\Support\Collection;

class UserStudentController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $fees = Fee::query()
            ->whereNotNull('name')
            ->paginate($request->per_page);

        return Inertia::render('Student/Billings/Index', [
            'fees' => FeeResource::collection($fees)
        ]);
    }

    public function submitFees(Request $request, User $student)
    {
        $validateFile = $request->validate([
            'file' => 'required'
        ]);
        // $data = $request->fees;
        // foreach ($data as  $fee) {
        //     foreach ($fee['meta'] as $meta) {
        //         if (intval($meta['amount']) - intval($meta['toPay']) == 0) {
        //             $meta['balance'] = 'paid';
        //         } else {
        //             $meta['balance'] = intval($meta['amount']) - intval($meta['toPay']);
        //         }
        //     }
        // }
        // $test = collect($request->fees);
        // $temp = $test->map(function (array $item, int $key) {
        //     // $item['meta']->map(function (array $meta, int $key) {
        //     //     dd($meta);
        //     // });
        //     foreach ($item['meta'] as $meta) {
        //         if (intval($meta['amount']) - intval($meta['toPay']) == 0) {
        //             $meta['balance'] = 'paid';
        //         } else {
        //             $meta['balance'] = intval($meta['amount']) - intval($meta['toPay']);
        //         }
        //     }
        // });
        // dd($data);
        // foreach ($data as  $index => $fee) {

        //     foreach ($fee['meta'] as $key => $meta) {
        //         if (intval($meta['amount']) - intval($meta['toPay']) == 0) {
        //             //  $meta['balance'] = 'paid';
        //             $test[$index][$key]['balance'] = 'paid';
        //         } else {
        //             // $meta['balance'] = intval($meta['amount']) - intval($meta['toPay']);
        //             $test[$index][$key]['balance'] = intval($meta['amount']) - intval($meta['toPay']);
        //         }
        //     }
        // }
        // dd($data);
        $history = History::create([
            'student_id' => $request->student['id'],
            'name' => $request->student['name'],
            'meta' => $request->fees,
            'file' => $validateFile['file'],
            'status' => 'pending'
        ]);

        return redirect()->back();
    }

    public function submitPayment(Request $request, User $student)
    {
        $student->update([
            'meta' => $request->meta,
        ]);
        $history = History::where('id', $request->transactionId)
            ->update([
                'status' => 'accepted'
            ]);

        $student->save();

        return redirect()->back();
    }

    public function declinePayment(Request $request, User $student)
    {
        $history = History::where('id', $request->transactionId)
            ->update([
                'status' => 'declined'
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
