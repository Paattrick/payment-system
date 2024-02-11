<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FeesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserStudentController;
use App\Http\Controllers\StudentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['prefix' => 'admin', 'middleware' => 'role:admin'], function () {
        Route::resource('/students', StudentController::class);
        Route::resource('/employees', EmployeeController::class);
        Route::resource('/fees', FeesController::class);

        Route::get('/students-filter', [StudentController::class, 'filterStudent'])->name('students-filter.index');
    });

    Route::group(['prefix' => 'student', 'middleware' => 'role:student'], function () {
        Route::get('/billings', [UserStudentController::class, 'index'])->name('billings.index');
        // Route::resource('/employees', EmployeeController::class);

        // Route::get('/students-filter', [StudentController::class, 'filterStudent'])->name('students-filter.index');
    });
});

Route::get('/student/login', [StudentController::class, 'login'])->name('student.login');
Route::post('/student/create', [StudentController::class, 'create'])->name('student.create');

require __DIR__ . '/auth.php';
