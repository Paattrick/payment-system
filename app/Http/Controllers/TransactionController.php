<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;
use App\Http\Resources\HistoryResource;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $transactions = History::query()
            ->whereNotNull('student_id')
            ->where('status', 'pending')
            ->paginate($request->per_page);

        return Inertia::render('Admin/Transactions/Index', [
            'transactions' => HistoryResource::collection($transactions),
        ]);
    }
}
