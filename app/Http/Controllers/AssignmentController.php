<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $assignments = Assignment::query();

        if($user->role == 'student'){
            $subject_ids = $user->student->subjects->pluck('id');
            $student_id = $user->student->id;
            $assignments = $assignments->with('answers', function ($q) use ($student_id){
                $q->where('student_id', $student_id);
            })->whereIn('subject_id', $subject_ids)->where('grade_id', $user->student->grade_id)->get();
        }

        if($user->role == 'teacher'){
            $assignments = $assignments->with('answers')->where('teacher_id', $user->teacher->id)->get();
        }

        if($user->role == 'admin'){
            $assignments = $assignments->with('answers')->get();
        }


        return responseJson('', ['assignments' =>  $assignments]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'assignment' => ['required', File::types(['pdf'])],
        ]);
        $validator->validate();
        $validated = $validator->safe()->all();

        $validated['assignment'] =  Storage::put('public/assignment', $request->file('assignment'));
        $validated['subject_id'] = auth()->user()->teacher->subject_id;
        $validated['grade_id'] = auth()->user()->teacher->grade_id;
        $validated['teacher_id'] = auth()->user()->teacher->id;
        $assignment = Assignment::create($validated);

        return responseJson('subject created successfully', [
            'subject' => $assignment,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assignment $assignment)
    {
        //
    }
}
