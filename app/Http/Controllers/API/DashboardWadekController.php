<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetImprovement;
use App\Models\Category;
use App\Models\QuarterYear;
use Illuminate\Http\Request;

class DashboardWadekController extends Controller
{
    //
    public function getComparisonOfTheNumberOfGoodAndRepairAssets(Request $request)
    {
        try {
            $year = $request->input('year');
            $queryYear = QuarterYear::where('year', $year)->first();

            $start_tw_1 = $queryYear->start_tw_1;
            $end_tw_1 = $queryYear->end_tw_1;

            $start_tw_2 = $queryYear->start_tw_2;
            $end_tw_2 = $queryYear->end_tw_2;

            $start_tw_3 = $queryYear->start_tw_3;
            $end_tw_3 = $queryYear->end_tw_3;

            $start_tw_4 = $queryYear->start_tw_4;
            $end_tw_4 = $queryYear->end_tw_4;

            $arrPerbaikan = ['Perbaikan Mandiri', 'Perbaikan Vendor'];

            $categories = Category::get();

            $result_data_tw_1 = [];
            $result_data_tw_2 = [];
            $result_data_tw_3 = [];
            $result_data_tw_4 = [];

            $result_labels = [];
            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                $totalPerbaikan1 = Asset::whereIn('status', $arrPerbaikan)->where('category_id', $id)->whereBetween('created_at', [$start_tw_1, $end_tw_1])->count();

                $totalPerbaikan2 = Asset::whereIn('status', $arrPerbaikan)->where('category_id', $id)->whereBetween('created_at', [$start_tw_2, $end_tw_2])->count();

                $totalPerbaikan3 = Asset::whereIn('status', $arrPerbaikan)->where('category_id', $id)->whereBetween('created_at', [$start_tw_3, $end_tw_3])->count();

                $totalPerbaikan4 = Asset::whereIn('status', $arrPerbaikan)->where('category_id', $id)->whereBetween('created_at', [$start_tw_4, $end_tw_4])->count();

                array_push($result_labels, $name . ' Perbaikan');

                array_push($result_data_tw_1, $totalPerbaikan1);
                array_push($result_data_tw_2, $totalPerbaikan2);
                array_push($result_data_tw_3, $totalPerbaikan3);
                array_push($result_data_tw_4, $totalPerbaikan4);
            }

            $datasets = [[
                'label' => 'Triwulan 1',
                'data' => $result_data_tw_1,
                'backgroundColor' => ['#156082']
            ], [
                'label' => 'Triwulan 2',
                'data' => $result_data_tw_2,
                'backgroundColor' => ['#e97132']
            ], [
                'label' => 'Triwulan 3',
                'data' => $result_data_tw_3,
                'backgroundColor' => ['#196b24']
            ], [
                'label' => 'Triwulan 4',
                'data' => $result_data_tw_4,
                'backgroundColor' => ['#0f9ed5']
            ]];

            $result = [
                'labels' => $result_labels,
                'datasets' => $datasets
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getTotalGoodAsset(Request $request)
    {
        try {
            $year = $request->input('year');
            $queryYear = QuarterYear::where('year', $year)->first();

            $start_tw_1 = $queryYear->start_tw_1;
            $end_tw_1 = $queryYear->end_tw_1;

            $start_tw_2 = $queryYear->start_tw_2;
            $end_tw_2 = $queryYear->end_tw_2;

            $start_tw_3 = $queryYear->start_tw_3;
            $end_tw_3 = $queryYear->end_tw_3;

            $start_tw_4 = $queryYear->start_tw_4;
            $end_tw_4 = $queryYear->end_tw_4;

            $arrBaik = ['Baik'];

            $categories = Category::get();

            $result_data_tw_1 = [];
            $result_data_tw_2 = [];
            $result_data_tw_3 = [];
            $result_data_tw_4 = [];

            $result_labels = [];
            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                $totalBaik1 = Asset::whereIn('status', $arrBaik)->where('category_id', $id)->whereBetween('created_at', [$start_tw_1, $end_tw_1])->count();

                $totalBaik2 = Asset::whereIn('status', $arrBaik)->where('category_id', $id)->whereBetween('created_at', [$start_tw_2, $end_tw_2])->count();

                $totalBaik3 = Asset::whereIn('status', $arrBaik)->where('category_id', $id)->whereBetween('created_at', [$start_tw_3, $end_tw_3])->count();

                $totalBaik4 = Asset::whereIn('status', $arrBaik)->where('category_id', $id)->whereBetween('created_at', [$start_tw_4, $end_tw_4])->count();

                array_push($result_labels, $name . ' Baik');

                array_push($result_data_tw_1, $totalBaik1);
                array_push($result_data_tw_2, $totalBaik2);
                array_push($result_data_tw_3, $totalBaik3);
                array_push($result_data_tw_4, $totalBaik4);
            }

            $datasets = [[
                'label' => 'Triwulan 1',
                'data' => $result_data_tw_1,
                'backgroundColor' => ['#156082']
            ], [
                'label' => 'Triwulan 2',
                'data' => $result_data_tw_2,
                'backgroundColor' => ['#e97132']
            ], [
                'label' => 'Triwulan 3',
                'data' => $result_data_tw_3,
                'backgroundColor' => ['#196b24']
            ], [
                'label' => 'Triwulan 4',
                'data' => $result_data_tw_4,
                'backgroundColor' => ['#0f9ed5']
            ]];

            $result = [
                'labels' => $result_labels,
                'datasets' => $datasets
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    // [
    //     {
    //       label: "Triwulan 1",
    //       data: [200, 200, 300, 400],
    //     },
    //     {
    //       label: "Triwulan 2",
    //       data: [200, 200, 300, 400],
    //     },
    //     {
    //       label: "Triwulan 3",
    //       data: [200, 200, 300, 400],
    //     },
    //     {
    //       label: "Triwulan 4",
    //       data: [200, 200, 300, 400],
    //     },
    //   ],

    public function getTotalAssetRepairFund(Request $request)
    {
        try {
            $year = $request->input('year');
            $queryYear = QuarterYear::where('year', $year)->first();

            $start_tw_1 = $queryYear->start_tw_1;
            $end_tw_1 = $queryYear->end_tw_1;

            $start_tw_2 = $queryYear->start_tw_2;
            $end_tw_2 = $queryYear->end_tw_2;

            $start_tw_3 = $queryYear->start_tw_3;
            $end_tw_3 = $queryYear->end_tw_3;

            $start_tw_4 = $queryYear->start_tw_4;
            $end_tw_4 = $queryYear->end_tw_4;

            $categories = Category::get();

            $result_headers = [""];
            $result_tw_1 = ["Triwulan 1"];
            $result_tw_2 = ["Triwulan 2"];
            $result_tw_3 = ["Triwulan 3"];
            $result_tw_4 = ["Triwulan 4"];

            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                array_push($result_headers, $name);

                $totalFund1 = AssetImprovement::select('improvement_price')
                    ->with('asset_category')
                    ->whereHas('asset_category', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_1, $end_tw_1])
                    ->sum('improvement_price');

                $totalFund2 = AssetImprovement::select('improvement_price')
                    ->with('asset_category')
                    ->whereHas('asset_category', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_2, $end_tw_2])
                    ->sum('improvement_price');

                $totalFund3 = AssetImprovement::select('improvement_price')
                    ->with('asset_category')
                    ->whereHas('asset_category', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_3, $end_tw_3])
                    ->sum('improvement_price');

                $totalFund4 = AssetImprovement::select('improvement_price')
                    ->with('asset_category')
                    ->whereHas('asset_category', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_4, $end_tw_4])
                    ->sum('improvement_price');

                array_push($result_tw_1, (float)$totalFund1);
                array_push($result_tw_2, (float)$totalFund2);
                array_push($result_tw_3, (float)$totalFund3);
                array_push($result_tw_4, (float)$totalFund4);
            }

            $result_repair = [
                $result_tw_1,
                $result_tw_2,
                $result_tw_3,
                $result_tw_4,
            ];

            $result = [
                'headers' => $result_headers,
                'body' => $result_repair
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getRepairTimeAsset(Request $request)
    {
        try {
            $year = $request->input('year');
            $queryYear = QuarterYear::where('year', $year)->first();

            $start_tw_1 = $queryYear->start_tw_1;
            $end_tw_1 = $queryYear->end_tw_1;

            $start_tw_2 = $queryYear->start_tw_2;
            $end_tw_2 = $queryYear->end_tw_2;

            $start_tw_3 = $queryYear->start_tw_3;
            $end_tw_3 = $queryYear->end_tw_3;

            $start_tw_4 = $queryYear->start_tw_4;
            $end_tw_4 = $queryYear->end_tw_4;

            $categories = Category::get();

            $result_headers = [""];
            $result_tw_1 = ["Triwulan 1"];
            $result_tw_2 = ["Triwulan 2"];
            $result_tw_3 = ["Triwulan 3"];
            $result_tw_4 = ["Triwulan 4"];

            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                array_push($result_headers, $name);

                $totalFund1 = AssetImprovement::select('asset_id', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_1, $end_tw_1])
                    ->get();


                $totalFund2 = AssetImprovement::select('asset_id', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_2, $end_tw_2])
                    ->get();

                $totalFund3 = AssetImprovement::select('asset_id', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_3, $end_tw_3])
                    ->get();

                $totalFund4 = AssetImprovement::select('asset_id', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_4, $end_tw_4])
                    ->get();

                array_push($result_tw_1, $totalFund1);
                array_push($result_tw_2, $totalFund2);
                array_push($result_tw_3, $totalFund3);
                array_push($result_tw_4, $totalFund4);
            }

            $result_repair = [
                $result_tw_1,
                $result_tw_2,
                $result_tw_3,
                $result_tw_4,
            ];

            $result = [
                'headers' => $result_headers,
                'body' => $result_repair
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getPercentageRepairAsset(Request $request)
    {
        try {
            $year = $request->input('year');
            $queryYear = QuarterYear::where('year', $year)->first();

            $start_tw_1 = $queryYear->start_tw_1;
            $end_tw_1 = $queryYear->end_tw_1;

            $start_tw_2 = $queryYear->start_tw_2;
            $end_tw_2 = $queryYear->end_tw_2;

            $start_tw_3 = $queryYear->start_tw_3;
            $end_tw_3 = $queryYear->end_tw_3;

            $start_tw_4 = $queryYear->start_tw_4;
            $end_tw_4 = $queryYear->end_tw_4;

            $categories = Category::get();

            $result_headers = [""];
            $result_tw_1 = ["Triwulan 1"];
            $result_tw_2 = ["Triwulan 2"];
            $result_tw_3 = ["Triwulan 3"];
            $result_tw_4 = ["Triwulan 4"];

            foreach ($categories as $category) {
                $id = $category->id;
                $name = $category->name;

                array_push($result_headers, $name);

                $totalFund1 = AssetImprovement::select('asset_id', 'type', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_1, $end_tw_1])
                    ->get();


                $totalFund2 = AssetImprovement::select('asset_id', 'type', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_2, $end_tw_2])
                    ->get();

                $totalFund3 = AssetImprovement::select('asset_id', 'type', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_3, $end_tw_3])
                    ->get();

                $totalFund4 = AssetImprovement::select('asset_id', 'type', 'actual_repair_start_date', 'actual_repair_end_date')
                    ->with(['asset_query' => function ($query) {
                        $query->select('id', 'category_id'); // Assuming 'id' is the foreign key in AssetImprovement
                    }])
                    ->whereHas('asset_query', function ($query) use ($id) {
                        $query->where('category_id', $id);
                    })
                    ->whereBetween('created_at', [$start_tw_4, $end_tw_4])
                    ->get();

                array_push($result_tw_1, $totalFund1);
                array_push($result_tw_2, $totalFund2);
                array_push($result_tw_3, $totalFund3);
                array_push($result_tw_4, $totalFund4);
            }

            $result_repair = [
                $result_tw_1,
                $result_tw_2,
                $result_tw_3,
                $result_tw_4,
            ];

            $result = [
                'headers' => $result_headers,
                'body' => $result_repair
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
