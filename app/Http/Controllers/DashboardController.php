<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\History;
use App\Http\Resources\StudentResource;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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
