<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;
use App\Http\Resources\HistoryResource;
use Illuminate\Support\Facades\Auth;

class UserHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $request->merge([
            'per_page' => $request->per_page ?: '15',
        ]);

        $histories = History::query()
            ->where('student_id', $user->id)
            ->latest()
            ->paginate($request->per_page);

        return Inertia::render('Student/History/Index', [
            'histories' => HistoryResource::collection($histories),
        ]);
    }
}
