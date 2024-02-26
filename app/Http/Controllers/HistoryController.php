<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;
use App\Http\Resources\HistoryResource;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $histories = History::query()
            ->whereNotNull('student_id')
            ->whereNot('status', 'pending')
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Admin/History/Index', [
            'histories' => HistoryResource::collection($histories),
        ]);
    }
}
