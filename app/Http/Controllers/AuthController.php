<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group AuthController
 */
class AuthController extends Controller
{
    /**
     * Register
     * @unauthenticated
     * @throws ValidationException
     */

    public function register(Request $request): JsonResponse
    {

        $rules = [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')],
            'password' => 'required',
            'device_name' => 'nullable',
            'role' => 'in:student,teacher,admin',
            'qualification' => Rule::requiredIf(fn () => $request->role == 'teacher'),
            'dob' => [
                Rule::requiredIf(fn () => $request->role == 'teacher'),
                'date',
            ],
            'subject_id' => [
                Rule::requiredIf(fn () => $request->role == 'teacher'),
                'exists:subjects,id',
            ],
            'subjects' => [
                Rule::requiredIf(fn () => $request->role == 'student'),
                'array',
            ],
            'subjects.*' => 'exists:subjects,id',
            'grade_id' => [
                Rule::requiredIf(fn () => $request->role == 'teacher' || $request->role == 'student'),
            ],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $validated = $validator->safe()->except('device_name');
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        if($request->role == 'teacher'){
            $teacher = Teacher::create([
                'grade_id' => $validated['grade_id'],
                'qualification' => $validated['qualification'],
                'dob' => $validated['dob'],
                'subject_id' => $validated['subject_id'],
                'user_id' => $user->id,
            ]);
        }

        if($request->role == 'student'){
            $student = Student::create([
                'grade_id' => $validated['grade_id'],
                'user_id' => $user->id,
            ]);

            foreach ($validated['subjects'] as $subject){
                $student->subjects()->attach($subject);
            }
        }


        $device_name = $validated['device_name'] ?? 'default';
        $token = $user->createToken($device_name)->plainTextToken;

        return responseJson('', ['token' => $token, 'user' => $user]);
    }

    /**
     * Login
     * @unauthenticated
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable',
        ])->validate();

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['provided credentials are incorrect'],
            ]);
        }

        $device_name = $request->device_name ?? 'default';

        $user->tokens()->where('name', $device_name)->delete();

        $token = $user->createToken($device_name)->plainTextToken;

        return responseJson('logged in successfully', [
            'user' => $user,
            'token' => $token,
        ]);

    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return responseJson('Logged out');
    }
}
