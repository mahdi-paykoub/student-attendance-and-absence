<?php

use App\Http\Controllers\{
    StudentController,
    GradeController,
    MajorController,
    SchoolController,
    ProvinceController,
    CityController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::resource('students', StudentController::class);
Route::resource('grades', GradeController::class);
Route::resource('majors', MajorController::class);
Route::resource('schools', SchoolController::class);
Route::resource('provinces', ProvinceController::class);
Route::resource('cities', CityController::class);
