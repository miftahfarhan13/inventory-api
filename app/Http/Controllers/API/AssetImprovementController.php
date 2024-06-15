<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AssetImprovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetImprovementController extends Controller
{
    //
    public function getAssetImprovements(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            if ($isPaginate) {
                $asset_improvements = AssetImprovement::with(relations: ['asset'])->paginate($request->per_page ?? 15);
            } else {
                $asset_improvements = AssetImprovement::with(relations: ['asset'])->get();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvements], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getAssetImprovementsByAssetId($assetId, Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;

            $search = $request->search;

            if ($isPaginate) {
                $asset_improvements = AssetImprovement::with(relations: ['asset', 'user', 'approved_user'])
                    ->whereHas('asset', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->where('asset_id', '=', $assetId)->paginate($request->per_page ?? 15);
            } else {
                $asset_improvements = AssetImprovement::with(relations: ['asset'])->get();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvements], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createAssetImprovement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|integer',
            'reporter' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $asset_improvement = new AssetImprovement();
            $asset_improvement->created_by = $user->id;
            $asset_improvement->asset_id = $request->input('asset_id');
            $asset_improvement->type = $request->input('type');
            $asset_improvement->status = $request->input('status');
            $asset_improvement->description = $request->input('description');
            $asset_improvement->reporter = $request->input('reporter');
            $asset_improvement->contact_reporter = $request->input('contact_reporter');
            $asset_improvement->contact_technician = $request->input('contact_technician');
            $asset_improvement->improvement_price = $request->input('improvement_price');
            $asset_improvement->additional_document = $request->input('additional_document');
            $asset_improvement->report_date = $request->input('report_date');
            $asset_improvement->validation_by_laboratory_date = $request->input('validation_by_laboratory_date');
            $asset_improvement->repair_time_plan_date = $request->input('repair_time_plan_date');
            $asset_improvement->actual_repair_start_date = $request->input('actual_repair_start_date');
            $asset_improvement->actual_repair_end_date = $request->input('actual_repair_end_date');


            $asset_improvement->save();

            $asset_improvement = AssetImprovement::where('id', $asset_improvement->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvement, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateAssetImprovement($assetImprovementId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_id' => 'required|integer',
            'reporter' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $asset_improvement = AssetImprovement::find($assetImprovementId);
            if (!$asset_improvement) {
                return response()->json(['error' => true, 'message' => 'AssetImprovement not found'], 406);
            }
            $asset_improvement->asset_id = $request->input('asset_id');
            $asset_improvement->type = $request->input('type');
            $asset_improvement->status = $request->input('status');
            $asset_improvement->description = $request->input('description');
            $asset_improvement->reporter = $request->input('reporter');
            $asset_improvement->contact_reporter = $request->input('contact_reporter');
            $asset_improvement->contact_technician = $request->input('contact_technician');
            $asset_improvement->improvement_price = $request->input('improvement_price');
            $asset_improvement->additional_document = $request->input('additional_document');
            $asset_improvement->report_date = $request->input('report_date');
            $asset_improvement->validation_by_laboratory_date = $request->input('validation_by_laboratory_date');
            $asset_improvement->repair_time_plan_date = $request->input('repair_time_plan_date');
            $asset_improvement->actual_repair_start_date = $request->input('actual_repair_start_date');
            $asset_improvement->actual_repair_end_date = $request->input('actual_repair_end_date');

            $asset_improvement->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvement, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateAssetImprovementStatus($assetImprovementId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        try {
            $asset_improvement = AssetImprovement::find($assetImprovementId);
            if (!$asset_improvement) {
                return response()->json(['error' => true, 'message' => 'AssetImprovement not found'], 406);
            }
            $asset_improvement->approved_by = $user->id;
            $asset_improvement->status = $request->input('status');

            $asset_improvement->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvement, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteAssetImprovement($assetImprovementId)
    {
        try {
            AssetImprovement::where('id', $assetImprovementId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
