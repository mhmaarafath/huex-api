<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $grades = Grade::all();
        return responseJson('', [
            'grades' => $grades,
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
            'name' => ['required', Rule::unique('grades')],
        ]);
        $validator->validate();
        $validated = $validator->safe()->all();
        $grade = Grade::create($validated);
        return responseJson('grade created successfully', [
            'grade' => $grade,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Grade $grade
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, Grade $grade): JsonResponse
    {
        $rules = [
            'name' => ['sometimes', 'required', Rule::unique('grades')->ignore($grade)],
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $validated = $validator->safe()->all();

        $grade->update($validated);

        return responseJson('grade updated successfully', [
            'grade' => $grade,
        ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Grade $grade
     * @return JsonResponse
     */
    public function destroy(Grade $grade): JsonResponse
    {
        $grade->delete();

        return responseJson('grade deleted successfully', [
            'subject' => $grade,
        ]);

    }


}
