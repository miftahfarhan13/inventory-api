<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AssetImprovement;
use App\Models\QuarterYear;
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
            $isTw1 = $request->is_tw_1;
            $isTw2 = $request->is_tw_2;
            $isTw3 = $request->is_tw_3;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $search = $request->search;
            $locationId = $request->location_id;
            $categoryId = $request->category_id;
            $studyProgramId = $request->study_program_id;
            $type = $request->type;
            $status = $request->status;
            $priceStart = $request->price_start;
            $priceEnd = $request->price_end;

            $currentYear = date("Y");

            $queryYear = QuarterYear::where('year', '=', $currentYear)->first();
            $startTw1 = $queryYear['start_tw_1'] ? $queryYear['start_tw_1'] : $currentYear . '01-01';
            $endTw1 = $queryYear['end_tw_1'] ? $queryYear['end_tw_1'] : $currentYear . '04-30';

            $startTw2 = $queryYear['start_tw_2'] ? $queryYear['start_tw_2'] : $currentYear . '05-01';
            $endTw2 = $queryYear['end_tw_2'] ? $queryYear['end_tw_2'] : $currentYear . '08-31';

            $startTw3 = $queryYear['start_tw_3'] ? $queryYear['start_tw_3'] : $currentYear . '09-01';
            $endTw3 = $queryYear['end_tw_3'] ? $queryYear['end_tw_3'] : $currentYear . '12-31';

            $query = AssetImprovement::with(relations: ['asset', 'user', 'approved_user']);
            
            if (!empty($priceStart) && !empty($priceEnd)) {
                $query->whereBetween('improvement_price', [(int)$priceStart, (int)$priceEnd]);
            }

            $arrayStatus = explode(",", $status);
            if (!empty($status) && is_array($arrayStatus)) {
                $query->whereIn('status', $arrayStatus);
            }

            if (!empty($type) ) {
                $query->where('type', '=', $type);
            }

            if (!empty($studyProgramId)) {
                $query->whereHas('asset', function ($que) use ($studyProgramId) {
                    $que->whereHas('location', function ($q) use ($studyProgramId) {
                        $q->where('locations.study_program_id', $studyProgramId);
                    });
                });
            }

            if (!empty($categoryId)) {
                $query->whereHas('asset', function ($q) use ($categoryId) {
                    $q->where('category_id', '=', $categoryId);
                });
            }

            if (!empty($locationId)) {
                $query->whereHas('asset', function ($q) use ($locationId) {
                    $q->where('location_id', '=', $locationId);
                });
            }

            if (!empty($isTw3)) {
                $query->whereBetween('actual_repair_end_date', [$startTw3, $endTw3]);
            }

            if (!empty($isTw2)) {
                $query->whereBetween('actual_repair_end_date', [$startTw2, $endTw2]);
            }

            if (!empty($isTw1)) {
                $query->whereBetween('actual_repair_end_date', [$startTw1, $endTw1]);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('actual_repair_end_date', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $query->whereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }

            if ($isPaginate) {
                $asset_improvements = $query->paginate($request->per_page ?? 15);
            } else {
                $asset_improvements = $query->get();
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
            $isTw1 = $request->is_tw_1;
            $isTw2 = $request->is_tw_2;
            $isTw3 = $request->is_tw_3;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $search = $request->search;
            $locationId = $request->location_id;
            $categoryId = $request->category_id;
            $studyProgramId = $request->study_program_id;
            $type = $request->type;
            $status = $request->status;
            $priceStart = $request->price_start;
            $priceEnd = $request->price_end;

            $currentYear = date("Y");

            $queryYear = QuarterYear::where('year', '=', $currentYear)->first();
            $startTw1 = $queryYear['start_tw_1'] ? $queryYear['start_tw_1'] : $currentYear . '01-01';
            $endTw1 = $queryYear['end_tw_1'] ? $queryYear['end_tw_1'] : $currentYear . '04-30';

            $startTw2 = $queryYear['start_tw_2'] ? $queryYear['start_tw_2'] : $currentYear . '05-01';
            $endTw2 = $queryYear['end_tw_2'] ? $queryYear['end_tw_2'] : $currentYear . '08-31';

            $startTw3 = $queryYear['start_tw_3'] ? $queryYear['start_tw_3'] : $currentYear . '09-01';
            $endTw3 = $queryYear['end_tw_3'] ? $queryYear['end_tw_3'] : $currentYear . '12-31';

            $query = AssetImprovement::with(relations: ['asset', 'user', 'approved_user']);
            
            if (!empty($priceStart) && !empty($priceEnd)) {
                $query->whereBetween('improvement_price', [(int)$priceStart, (int)$priceEnd]);
            }

            $arrayStatus = explode(",", $status);
            if (!empty($status) && is_array($arrayStatus)) {
                $query->whereIn('status', $arrayStatus);
            }

            if (!empty($type) ) {
                $query->where('type', '=', $type);
            }

            if (!empty($studyProgramId)) {
                $query->whereHas('asset', function ($que) use ($studyProgramId) {
                    $que->whereHas('location', function ($q) use ($studyProgramId) {
                        $q->where('locations.study_program_id', $studyProgramId);
                    });
                });
            }

            if (!empty($categoryId)) {
                $query->whereHas('asset', function ($q) use ($categoryId) {
                    $q->where('category_id', '=', $categoryId);
                });
            }

            if (!empty($locationId)) {
                $query->whereHas('asset', function ($q) use ($locationId) {
                    $q->where('location_id', '=', $locationId);
                });
            }

            if (!empty($isTw3)) {
                $query->whereBetween('actual_repair_end_date', [$startTw3, $endTw3]);
            }

            if (!empty($isTw2)) {
                $query->whereBetween('actual_repair_end_date', [$startTw2, $endTw2]);
            }

            if (!empty($isTw1)) {
                $query->whereBetween('actual_repair_end_date', [$startTw1, $endTw1]);
            }

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('actual_repair_end_date', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $query->whereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            }

            if ($isPaginate) {
                $asset_improvements = $query->where('asset_id', '=', $assetId)->paginate($request->per_page ?? 15);
            } else {
                $asset_improvements = $query->get();
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
