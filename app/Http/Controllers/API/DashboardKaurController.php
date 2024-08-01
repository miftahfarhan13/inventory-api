<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetImprovement;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardKaurController extends Controller
{
    //
    public function getTotalAssetStatusByCategory(Request $request)
    {
        try {
            $year = $request->input('year');

            $arrBaik = ['Baik'];
            $arrPerbaikan = ['Perbaikan Mandiri', 'Perbaikan Vendor'];

            $categories = Category::get();

            $results = [];
            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                $total_baik = Asset::whereIn('status', $arrBaik)->where('category_id', $id)->whereYear('created_at', $year)->count();
                $total_perbaikan = Asset::whereIn('status', $arrPerbaikan)->where('category_id', $id)->whereYear('created_at', $year)->count();

                $total_asset = [
                    'labels' => ['Baik', 'Diperbaiki'],
                    'datasets' => [
                        [
                            'label' => 'Total Aset',
                            'data' => [$total_baik, $total_perbaikan],
                            'backgroundColor' => ['#156082', '#e97132']
                        ]
                    ],
                ];

                $result_category = [
                    'category' => $name,
                    'data' => $total_asset
                ];

                array_push($results, $result_category);
            }

            //return successful response
            return response()->json(['error' => false, 'result' => $results], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getAssetImprovementsAdmin2(Request $request)
    {
        try {
            $isPaginate = !empty($request->is_paginate) ? filter_var($request->query('is_paginate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : true;
            $year = $request->year;
            $status = ['Tolak', 'Menunggu Persetujuan'];

            $query = AssetImprovement::with(relations: ['asset', 'user', 'approved_user'])->whereYear('asset_needed_date', $year)->whereIn('status', $status);

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
}
