<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    //
    public function getLocations(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            if ($isPaginate) {
                $locations = Location::with(relations: 'study_program')->paginate($request->per_page ?? 15);
            } else {
                $locations = Location::with(relations: 'study_program')->get();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $locations], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'study_program_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $location = new Location();
            $location->name = $request->input('name');
            $location->study_program_id = $request->input('study_program_id');
            $location->created_by = $user->id;
            $location->save();

            $location = Location::where('id', $location->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $location, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateLocation($locationId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'study_program_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $location = Location::find($locationId);
            if (!$location) {
                return response()->json(['error' => true, 'message' => 'Category not found'], 406);
            }
            $location->name = $request->input('name');
            $location->study_program_id = $request->input('study_program_id');

            $location->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $location, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteLocation($locationId)
    {
        try {
            Location::where('id', $locationId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
