<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'assignment_id' => ['required'],
            'answer' => ['required', File::types(['pdf'])],
        ]);
        $validator->validate();
        $validated = $validator->safe()->all();

        $assignment = Assignment::find($validated['assignment_id']);
        if($assignment){
            $subject_ids = $user->student->subjects->pluck('id')->toArray();

            if($assignment->grade_id == $user->student->grade_id && in_array($assignment->subject_id, $subject_ids)){
                $validated['student_id'] = $user->student->id;
                $validated['answer'] =  Storage::put('public/answer', $request->file('answer'));
                $answer = Answer::create($validated);
                return responseJson('answer submitted successfully', [
                    'answer' => $answer,
                ]);
            } else {
                return responseJson('unautharized');
            }

        } else {
            return responseJson('assignment not found');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Answer $answer)
    {
        $user = auth()->user();
        if($answer->student_id == $user->student->id){
            $validator = Validator::make($request->all(), [
                'answer' => ['required', File::types(['pdf'])],
            ]);
            $validator->validate();
            $validated = $validator->safe()->all();

            $validated['answer'] =  Storage::put('public/answer', $request->file('answer'));
            $answer->update($validated);
            return responseJson('answer updated successfully', [
                'answer' => $answer,
            ]);
        } else {
            return responseJson('unautharized');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Answer $answer)
    {
        $user = auth()->user();
        if($answer->student_id == $user->student->id){
            $answer->delete();
            return responseJson('answer removed');
        } else {
            return responseJson('unautharized');
        }
    }
}
