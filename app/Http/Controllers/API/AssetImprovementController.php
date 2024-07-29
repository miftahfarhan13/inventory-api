<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
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

            $query = AssetImprovement::with(relations: ['asset', 'user', 'approved_user']);

            if (!empty($priceStart) && !empty($priceEnd)) {
                $query->whereBetween('improvement_price', [(int)$priceStart, (int)$priceEnd]);
            }

            $arrayStatus = explode(",", $status);
            if (!empty($status) && is_array($arrayStatus)) {
                $query->whereIn('status', $arrayStatus);
            }

            if (!empty($type)) {
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

            $query = AssetImprovement::with(relations: ['asset', 'user', 'approved_user']);

            if (!empty($priceStart) && !empty($priceEnd)) {
                $query->whereBetween('improvement_price', [(int)$priceStart, (int)$priceEnd]);
            }

            $arrayStatus = explode(",", $status);
            if (!empty($status) && is_array($arrayStatus)) {
                $query->whereIn('status', $arrayStatus);
            }

            if (!empty($type)) {
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
                $asset_improvements = $query->where('asset_id', '=', $assetId)->get();
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

        if (!$user->id) {
            return response()->json(['error' => true, 'message' => "Not Authenticated, Please Relogin"], 406);
        }

        try {
            $asset_improvement = new AssetImprovement();
            $asset_improvement->created_by = $user->id;
            $asset_improvement->asset_id = $request->input('asset_id');
            $asset_improvement->type = $request->input('type');
            $asset_improvement->status = $request->input('status');
            $asset_improvement->description = $request->input('description');
            $asset_improvement->reporter = $request->input('reporter');
            $asset_improvement->contact_reporter = $request->input('contact_reporter');
            $asset_improvement->technician_name = $request->input('technician_name');
            $asset_improvement->improvement_price = $request->input('improvement_price');
            $asset_improvement->additional_document = $request->input('additional_document');
            $asset_improvement->report_date = $request->input('report_date');
            $asset_improvement->validation_by_laboratory_date = $request->input('validation_by_laboratory_date');
            $asset_improvement->repair_time_plan_date = $request->input('repair_time_plan_date');
            $asset_improvement->actual_repair_start_date = $request->input('actual_repair_start_date');
            $asset_improvement->actual_repair_end_date = $request->input('actual_repair_end_date');
            $asset_improvement->revision = $request->input('revision');
            $asset_improvement->urgency = $request->input('urgency');
            $asset_improvement->asset_needed_date = $request->input('asset_needed_date');
            $asset_improvement->target_repair_date = $request->input('target_repair_date');
            $asset_improvement->save();

            $asset_improvement = AssetImprovement::where('id', $asset_improvement->id)->first();

            $asset = Asset::find($request->input('asset_id'));
            $asset->status = $request->input('type');
            $asset->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvement, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createBulkAssetImprovement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'integer',
            'reporter' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $user = Auth::user();

        if (!$user->id) {
            return response()->json(['error' => true, 'message' => "Not Authenticated, Please Relogin"], 406);
        }

        try {
            $asset_ids = $request->input('asset_ids');

            foreach ($asset_ids as $asset_id) {
                $asset_improvement = new AssetImprovement();
                $asset_improvement->created_by = $user->id;
                $asset_improvement->asset_id = $asset_id;
                $asset_improvement->type = $request->input('type');
                $asset_improvement->status = $request->input('status');
                $asset_improvement->description = $request->input('description');
                $asset_improvement->reporter = $request->input('reporter');
                $asset_improvement->contact_reporter = $request->input('contact_reporter');
                $asset_improvement->technician_name = $request->input('technician_name');
                $asset_improvement->improvement_price = $request->input('improvement_price');
                $asset_improvement->additional_document = $request->input('additional_document');
                $asset_improvement->report_date = $request->input('report_date');
                $asset_improvement->validation_by_laboratory_date = $request->input('validation_by_laboratory_date');
                $asset_improvement->repair_time_plan_date = $request->input('repair_time_plan_date');
                $asset_improvement->actual_repair_start_date = $request->input('actual_repair_start_date');
                $asset_improvement->actual_repair_end_date = $request->input('actual_repair_end_date');
                $asset_improvement->revision = $request->input('revision');
                $asset_improvement->urgency = $request->input('urgency');
                $asset_improvement->asset_needed_date = $request->input('asset_needed_date');
                $asset_improvement->target_repair_date = $request->input('target_repair_date');
                $asset_improvement->save();

                $asset = Asset::find($asset_id);
                $asset->status = $request->input('type');
                $asset->save();
            }

            //return successful response
            return response()->json(['error' => false, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateAssetImprovement($assetImprovementId, Request $request)
    {
        $validator = Validator::make($request->all(), [
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
            $asset_improvement->type = $request->input('type');
            $asset_improvement->status = $request->input('status');
            $asset_improvement->description = $request->input('description');
            $asset_improvement->reporter = $request->input('reporter');
            $asset_improvement->contact_reporter = $request->input('contact_reporter');
            $asset_improvement->technician_name = $request->input('technician_name');
            $asset_improvement->improvement_price = $request->input('improvement_price');
            $asset_improvement->additional_document = $request->input('additional_document');
            $asset_improvement->report_date = $request->input('report_date');
            $asset_improvement->validation_by_laboratory_date = $request->input('validation_by_laboratory_date');
            $asset_improvement->repair_time_plan_date = $request->input('repair_time_plan_date');
            $asset_improvement->actual_repair_start_date = $request->input('actual_repair_start_date');
            $asset_improvement->actual_repair_end_date = $request->input('actual_repair_end_date');
            $asset_improvement->revision = $request->input('revision');
            $asset_improvement->urgency = $request->input('urgency');
            $asset_improvement->asset_needed_date = $request->input('asset_needed_date');
            $asset_improvement->target_repair_date = $request->input('target_repair_date');

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
            if ($request->input('status') === "Setuju") {
                $asset_improvement->approved_by = $user->id;
            }
            $asset_improvement->status = $request->input('status');
            $asset_improvement->revision = $request->input('revision');

            $asset_improvement->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset_improvement, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateAssetImprovementDates($assetImprovementId, Request $request)
    {
        $asset_id = $request->input('asset_id');
        $target_repair_date = $request->input('target_repair_date');
        $actual_repair_start_date = $request->input('actual_repair_start_date');
        $actual_repair_end_date = $request->input('actual_repair_end_date');

        try {
            $asset_improvement = AssetImprovement::find($assetImprovementId);
            if (!$asset_improvement) {
                return response()->json(['error' => true, 'message' => 'AssetImprovement not found'], 406);
            }

            if (!empty($target_repair_date)) $asset_improvement->target_repair_date = $target_repair_date;
            if (!empty($actual_repair_start_date)) $asset_improvement->actual_repair_start_date = $actual_repair_start_date;
            if (!empty($actual_repair_end_date)) {
                $asset = Asset::find($asset_id);
                $asset->status = "Baik";
                $asset->save();
                $asset_improvement->actual_repair_end_date = $actual_repair_end_date;
            }

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
