<?php

use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware(['optional.sanctum'])->group(function () {
    Route::get('modules', [\App\Http\Controllers\ServiceController::class, 'modules']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('subjects', [\App\Http\Controllers\SubjectController::class, 'index']);
    Route::get('assignments', [\App\Http\Controllers\AssignmentController::class, 'index']);
    Route::put('update-profile', [\App\Http\Controllers\ServiceController::class, 'updateProfile']);

    Route::middleware(['student'])->group(function (){
        Route::apiResource('answers', \App\Http\Controllers\AnswerController::class)->except('index', 'show');
        Route::get('students/subjects', [\App\Http\Controllers\ServiceController::class, 'studentsSubjects']);
        Route::delete('students/subjects/{subject}', [\App\Http\Controllers\ServiceController::class, 'studentsSubjectsDetach']);
        Route::patch('students/subjects/{subject}', [\App\Http\Controllers\ServiceController::class, 'studentsSubjectsAttach']);
        Route::patch('students/subjects/{subject}/favourite', [\App\Http\Controllers\ServiceController::class, 'studentSubjectsFavourites']);
    });

    Route::middleware(['admin'])->group(function (){
        Route::apiResource('subjects',\App\Http\Controllers\SubjectController::class)->except('index');
        Route::patch('users/{user}/toggle-user-state', [\App\Http\Controllers\ServiceController::class, 'toggleUserState']);
    });

    Route::middleware(['teacher'])->group(function (){
        Route::patch('answers/{answer}/marks', [\App\Http\Controllers\ServiceController::class, 'answerMarks']);
        Route::put('assignments/{assignment}/ranks', [\App\Http\Controllers\ServiceController::class, 'assignmentRanks']);
        Route::get('teachers/students', [\App\Http\Controllers\ServiceController::class, 'teachersStudents']);
        Route::post('assignments', [\App\Http\Controllers\AssignmentController::class, 'store']);
    });

});


