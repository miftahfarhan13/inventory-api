<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudyProgramController extends Controller
{
    //
    public function getStudyPrograms(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            if ($isPaginate) {
                $study_programs = StudyProgram::paginate($request->per_page ?? 15);
            } else {
                $study_programs = StudyProgram::all();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $study_programs], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getStudyProgramsWithLocations(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            if ($isPaginate) {
                $study_programs = StudyProgram::with(relations: 'locations')->paginate($request->per_page ?? 15);
            } else {
                $study_programs = StudyProgram::with(relations: 'locations')->get();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $study_programs], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createStudyProgram(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $study_program = new StudyProgram();
            $study_program->name = $request->input('name');
            $study_program->created_by = $user->id;
            $study_program->save();

            $study_program = StudyProgram::where('id', $study_program->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $study_program, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateStudyProgram($studyProgramId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $study_program = StudyProgram::find($studyProgramId);
            if (!$study_program) {
                return response()->json(['error' => true, 'message' => 'Category not found'], 406);
            }
            $study_program->name = $request->input('name');

            $study_program->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $study_program, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteStudyProgram($studyProgramId)
    {
        try {
            StudyProgram::where('id', $studyProgramId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
