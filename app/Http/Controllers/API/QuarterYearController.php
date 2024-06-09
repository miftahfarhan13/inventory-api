<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\QuarterYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuarterYearController extends Controller
{
    //
    public function getQuarterYears(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            $search = $request->search;

            if ($isPaginate) {
                $quarter_years = QuarterYear::with(relations: 'user')->where('year', 'like', '%'.$search.'%')->paginate($request->per_page ?? 15);
            } else {
                $quarter_years = QuarterYear::all();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $quarter_years], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createQuarterYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string',
            'start_tw_1' => 'required|date',
            'end_tw_1' => 'required|date',
            'start_tw_2' => 'required|date',
            'end_tw_2' => 'required|date',
            'start_tw_3' => 'required|date',
            'end_tw_3' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $year = QuarterYear::where('year', '=', $request->input('year'))->first();
            if ($year) {
                return response()->json(['error' => true, 'message' => 'Year already exist, please choose another year'], 406);
            }

            $quarter_year = new QuarterYear();
            $quarter_year->created_by = $user->id;
            $quarter_year->year = $request->input('year');
            $quarter_year->start_tw_1 = $request->input('start_tw_1');
            $quarter_year->end_tw_1 = $request->input('end_tw_1');
            $quarter_year->start_tw_2 = $request->input('start_tw_2');
            $quarter_year->end_tw_2 = $request->input('end_tw_2');
            $quarter_year->start_tw_3 = $request->input('start_tw_3');
            $quarter_year->end_tw_3 = $request->input('end_tw_3');
            $quarter_year->save();

            $quarter_year = QuarterYear::where('id', $quarter_year->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $quarter_year, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateQuarterYear($quarterYearId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string',
            'start_tw_1' => 'required|date',
            'end_tw_1' => 'required|date',
            'start_tw_2' => 'required|date',
            'end_tw_2' => 'required|date',
            'start_tw_3' => 'required|date',
            'end_tw_3' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $quarter_year = QuarterYear::find($quarterYearId);
            if (!$quarter_year) {
                return response()->json(['error' => true, 'message' => 'QuarterYear not found'], 406);
            }
            $quarter_year->year = $request->input('year');
            $quarter_year->start_tw_1 = $request->input('start_tw_1');
            $quarter_year->end_tw_1 = $request->input('end_tw_1');
            $quarter_year->start_tw_2 = $request->input('start_tw_2');
            $quarter_year->end_tw_2 = $request->input('end_tw_2');
            $quarter_year->start_tw_3 = $request->input('start_tw_3');
            $quarter_year->end_tw_3 = $request->input('end_tw_3');

            $quarter_year->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $quarter_year, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteQuarterYear($quarterYearId)
    {
        try {
            QuarterYear::where('id', $quarterYearId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
