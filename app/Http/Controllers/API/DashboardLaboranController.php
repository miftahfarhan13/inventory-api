<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AssetImprovement;
use DateTime;
use Illuminate\Http\Request;

class DashboardLaboranController extends Controller
{
    //
    public function getNearestSchedulesRepairAsset(Request $request)
    {
        try {
            $year = $request->year;

            $asset_improvements = AssetImprovement::select('asset_id', 'created_at')
                ->with(['asset' => function ($query) {
                    $query->select('id', 'asset_code', 'name');
                }])
                ->whereYear('created_at', $year)->get();

            $results = [];
            $today = new DateTime();
            $sevenDaysAhead = (clone $today)->modify('+7 days');

            foreach ($asset_improvements as $asset_improvement) {
                $createdAt = new DateTime($asset_improvement->created_at);
                $createdAtPlus30Days = (clone $createdAt)->modify('+30 days');

                if ($createdAtPlus30Days > $today && $createdAtPlus30Days <= $sevenDaysAhead) {
                    $dayDifferenceTodayWithCreatedAtPlus30Days = $today->diff($createdAtPlus30Days)->days;

                    array_push($results, [
                        'asset_code' => $asset_improvement->asset->asset_code,
                        'name' => $asset_improvement->asset->name,
                        'date' => $asset_improvement->created_at,
                        'dateNext' => $createdAtPlus30Days->format('Y-m-d'),
                        'day' => $dayDifferenceTodayWithCreatedAtPlus30Days
                    ]);
                }
            }

            // Return successful response
            return response()->json(['error' => false, 'result' => $results], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }



    public function getCurrentAssetRepairStatus(Request $request)
    {
        try {
            $year = $request->year;

            $asset_improvements = AssetImprovement::select('asset_id', 'created_at', 'type', 'target_repair_date')
                ->with(['asset' => function ($query) {
                    $query->select('id', 'asset_code', 'name');
                }])
                ->whereYear('target_repair_date', $year)->get();

            $results = [];
            $today = new DateTime();
            $sevenDaysAhead = (clone $today)->modify('+7 days');

            foreach ($asset_improvements as $asset_improvement) {
                $target_repair_date = new DateTime($asset_improvement->target_repair_date);

                if ($target_repair_date > $today && $target_repair_date <= $sevenDaysAhead) {
                    array_push($results, [
                        'asset_code' => $asset_improvement->asset->asset_code,
                        'name' => $asset_improvement->asset->name,
                        'type' => $asset_improvement->type,
                        'date' => $asset_improvement->target_repair_date,
                    ]);
                }
            }

            // Return successful response
            return response()->json(['error' => false, 'result' => $results], 200);
        } catch (\Exception $e) {
            // Return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
