<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ServiceController extends Controller
{
    public function updateProfile(Request $request){
        $user = auth()->user();
        $rules = [
            'name' => 'sometimes|required',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user)],
        ];
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();
        $validated = $validator->safe()->all();

        $validated['password'] = $request->update_password ?  Hash::make($request->password) : $user->password;

        $user->update($validated);

        return responseJson('user updated successfully', [
            'user' => $user,
        ]);
    }

    public function modules(): \Illuminate\Http\JsonResponse
    {
        $modules = \App\Models\Teacher::query()->with(['subject:id,name', 'grade:id,name'])->select('grade_id', 'subject_id', 'id');
        if(auth()->user()){
            $user = auth()->user();
            if($user->role == 'admin'){
                $modules = $modules;
            }

            if($user->role == 'teacher'){
                $modules = $modules->where('id', $user->teacher->id);
            }

            if($user->role == 'student'){
                $subject_ids = $user->student->subjects->pluck('id');
                $modules = $modules->whereIn('subject_id', $subject_ids)->where('grade_id', $user->student->grade_id);
            }
            $modules = $modules->get();
            foreach ($modules as $module){
                $module['assignments'] = \App\Models\Assignment::where([
                    'subject_id' => $module->subject_id,
                    'grade_id' => $module->grade_id,
                    'teacher_id' => $module->id,
                ])->select('assignment')->get();
            }

        } else {
            $modules = $modules->get();
        }
        return responseJson('', [
            'modules' => $modules,
        ]);
    }

    public function studentsSubjects(){
        return responseJson('', [
            'subjects' => auth()->user()->student->subjects,
        ]);
    }

    public function studentsSubjectsDetach(\App\Models\Subject $subject){
        auth()->user()->student->subjects()->detach($subject);
        return responseJson('subject removed');
    }

    public function studentsSubjectsAttach(\App\Models\Subject $subject){
        auth()->user()->student->subjects()->detach($subject);
        auth()->user()->student->subjects()->attach($subject);
        return responseJson('subject added');
    }

    public function studentSubjectsFavourites(\App\Models\Subject $subject){
        $subject = auth()->user()->student->subjects->find($subject);
        if($subject){
            $subjects = auth()->user()->student->subjects();
            $subjects->update([
                'is_favourite' => 0,
            ]);
            $student_subject = \App\Models\StudentSubject::where([
                'subject_id' => $subject->id,
                'student_id'=> auth()->user()->student->id,
            ])->first();
            $student_subject->update([
                'is_favourite' => 1,
            ]);
            return responseJson('marked as favourite');
        } else {
            return responseJson('invalid subject');
        }
    }

    public function toggleUserState($id){
        $user = \App\Models\User::withTrashed()->find($id);
        if($user->role == 'admin'){
            return responseJson('unautharized');
        } else {
            $user->trashed() ? $user->restore() : $user->delete();
            return responseJson('', ['user' => $user,]);
        }
    }

    public function answerMarks(Request $request, \App\Models\Answer $answer){
        $teacher = auth()->user()->teacher;
        if($answer->assignment->teacher_id == $teacher->id){
            $validator = Validator::make($request->all(), [
                'marks' => ['required','min:0', 'max:100','integer'],
            ]);
            $validator->validate();
            $validated = $validator->safe()->all();

            $answer->update([
                'marks' => $validated['marks'],
            ]);
            return responseJson('marks added successfully', [
                'answer' => $answer,
            ]);
        } else {
            return responseJson('unautharized');
        }
    }

    public function assignmentRanks(Request $request, Assignment $assignment){
        $teacher = auth()->user()->teacher;
        if($assignment->teacher_id == $teacher->id){
            $answers = \App\Models\Answer::where('assignment_id', $assignment->id)->orderBy('marks', 'desc')->get();
            $rank = 0;
            foreach ($answers as $answer){
                $rank++;
                $answer->update(['rank' => $rank]);
            }
            return responseJson('', ['answers' => $answers]);
        } else {
            return responseJson('unautharized');
        }
    }

    public function teachersStudents(){
        $teacher = auth()->user()->teacher;
        return responseJson('', [
            'students' => \App\Models\Student::where('grade_id', $teacher->grade_id)->whereRelation('subjects', 'subject_id', $teacher->subject_id)->get(),
        ]);
    }
}
