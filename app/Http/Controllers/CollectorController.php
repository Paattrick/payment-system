<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class CollectorController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Collector/Transactions/Index');
    }
}
