<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\QuarterYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    //
    public function getAssetById($assetId)
    {
        try {
            $asset = Asset::with(relations: ['category', 'location', 'asset_improvements'])->where('id', '=', $assetId)->first();
            //return successful response
            return response()->json(['error' => false, 'result' => $asset], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getAssets(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            $search = $request->search;
            $locationId = $request->location_id;
            $categoryId = $request->category_id;
            $studyProgramId = $request->study_program_id;
            $assetImprovementType = $request->asset_improvement_type;
            $isTw1 = $request->is_tw_1;
            $isTw2 = $request->is_tw_2;
            $isTw3 = $request->is_tw_3;
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $currentYear = date("Y");

            $queryYear = QuarterYear::where('year', '=', $currentYear)->first();
            $startTw1 = $queryYear['start_tw_1'] ? $queryYear['start_tw_1'] : $currentYear . '01-01';
            $endTw1 = $queryYear['end_tw_1'] ? $queryYear['end_tw_1'] : $currentYear . '04-30';

            $startTw2 = $queryYear['start_tw_2'] ? $queryYear['start_tw_2'] : $currentYear . '05-01';
            $endTw2 = $queryYear['end_tw_2'] ? $queryYear['end_tw_2'] : $currentYear . '08-31';

            $startTw3 = $queryYear['start_tw_3'] ? $queryYear['start_tw_3'] : $currentYear . '09-01';
            $endTw3 = $queryYear['end_tw_3'] ? $queryYear['end_tw_3'] : $currentYear . '12-31';

            $query = Asset::with(relations: ['category', 'location', 'user', 'asset_improvements']);

            if (!empty($startDate) && !empty($endDate)) {
                $query->whereHas('asset_improvements', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('asset_improvements.actual_repair_end_date', [$startDate, $endDate]);
                });
            }

            if (!empty($isTw3)) {
                $query->whereHas('asset_improvements', function ($q) use ($startTw3, $endTw3) {
                    $q->whereBetween('asset_improvements.actual_repair_end_date', [$startTw3, $endTw3]);
                });
            }

            if (!empty($isTw2)) {
                $query->whereHas('asset_improvements', function ($q) use ($startTw2, $endTw2) {
                    $q->whereBetween('asset_improvements.actual_repair_end_date', [$startTw2, $endTw2]);
                });
            }

            if (!empty($isTw1)) {
                $query->whereHas('asset_improvements', function ($q) use ($startTw1, $endTw1) {
                    $q->whereBetween('asset_improvements.actual_repair_end_date', [$startTw1, $endTw1]);
                });
            }

            $arrayTypes = explode(",", $assetImprovementType);
            if (!empty($assetImprovementType) && is_array($arrayTypes)) {
                $query->whereHas('asset_improvements', function ($q) use ($arrayTypes) {
                    $q->whereIn('asset_improvements.type', $arrayTypes);
                });
            }

            if (!empty($studyProgramId)) {
                $query->whereHas('location', function ($q) use ($studyProgramId) {
                    $q->where('locations.study_program_id', $studyProgramId);
                });
            }

            if (!empty($categoryId)) {
                $query->where('category_id', '=', $categoryId);
            }

            if (!empty($locationId)) {
                $query->where('location_id', '=', $locationId);
            }

            if (!empty($search)) {
                $query->where('asset_code', 'like', '%' . $search . '%');
            }

            if ($isPaginate) {
                $assets = $query->paginate($request->per_page ?? 15);
            } else {
                $assets = $query->get();
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $assets], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function createAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'asset_code' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        $get_asset = Asset::where('asset_code', '=', $request->asset_code)->first();
        if ($get_asset) {
            return response()->json(['error' => true, 'message' => "Kode aset sudah terpakai, silahkan masukkan kode aset lain"], 406);
        }

        $user = Auth::user();

        try {
            $asset = new Asset();
            $asset->created_by = $user->id;
            $asset->category_id = $request->input('category_id');
            $asset->location_id = $request->input('location_id');
            $asset->asset_code = $request->input('asset_code');
            $asset->name = $request->input('name');
            $asset->brand = $request->input('brand');
            $asset->vendor = $request->input('vendor');
            $asset->image_url = $request->input('image_url');
            $asset->price = $request->input('price');
            $asset->purchase_date = $request->input('purchase_date');
            $asset->routine_repair_time = $request->input('routine_repair_time');

            $asset->save();

            $asset = Asset::where('id', $asset->id)->first();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function updateAsset($assetId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'location_id' => 'required|integer',
            'asset_code' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => $validator->errors()->first()], 406);
        }

        try {
            $asset = Asset::find($assetId);
            if (!$asset) {
                return response()->json(['error' => true, 'message' => 'Asset not found'], 406);
            }
            $asset->category_id = $request->input('category_id');
            $asset->location_id = $request->input('location_id');
            $asset->asset_code = $request->input('asset_code');
            $asset->name = $request->input('name');
            $asset->brand = $request->input('brand');
            $asset->vendor = $request->input('vendor');
            $asset->image_url = $request->input('image_url');
            $asset->price = $request->input('price');
            $asset->purchase_date = $request->input('purchase_date');
            $asset->routine_repair_time = $request->input('routine_repair_time');

            $asset->save();

            //return successful response
            return response()->json(['error' => false, 'result' => $asset, 'message' => 'data saved'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function deleteAsset($assetId)
    {
        try {
            Asset::where('id', $assetId)->delete();
            //return successful response
            return response()->json(['error' => false, 'message' => 'data deleted'], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
