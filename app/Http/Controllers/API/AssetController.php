<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
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

            if ($isPaginate) {
                $assets = Asset::with(relations: ['category', 'location', 'user'])->paginate($request->per_page ?? 15);
            } else {
                $assets = Asset::with(relations: ['category', 'location', 'user'])->get();
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
            $asset->repair_date = $request->input('repair_date');

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
            $asset->repair_date = $request->input('repair_date');

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
