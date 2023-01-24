<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $subjects = Subject::all();
        return responseJson('', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('subjects')],
        ]);
        $validator->validate();
        $validated = $validator->safe()->all();
        $subject = Subject::create($validated);
        return responseJson('subject created successfully', [
            'subject' => $subject,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param Subject $subject
     * @return JsonResponse
     */
    public function show(Subject $subject): JsonResponse
    {
        return responseJson('', [
            'subject' => $subject,
        ]);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Subject $subject
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Subject $subject)
    {
        $rules = [
            'name' => ['sometimes', 'required', Rule::unique('subjects')->ignore($subject)],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $validated = $validator->safe()->all();

        $subject->update($validated);

        return responseJson('subject updated successfully', [
            'subject' => $subject,
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subject $subject
     * @return JsonResponse
     */
    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return responseJson('subject deleted successfully', [
            'subject' => $subject,
        ]);

    }
}
