<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetImprovement;
use App\Models\Location;
use App\Models\QuarterYear;
use App\Models\StudyProgram;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function getTotalAssetByStudyProgram()
    {
        try {
            $study_programs = StudyProgram::all();

            foreach ($study_programs as $study_program) {
                $name = $study_program->name;
                $study_program_id = $study_program->id;
                $count_asset = Asset::with(relations: ['location'])->whereHas('location', function ($q) use ($study_program_id) {
                    $q->where('locations.study_program_id', '=', $study_program_id);
                })->count();

                $locations = Location::where('study_program_id', '=', $study_program_id)->get();
                $results_location = [];
                foreach ($locations as $location) {
                    $location_name = $location->name;
                    $location_id = $location->id;
                    $count_asset_location = Asset::where('location_id', '=', $location_id)->count();

                    array_push($results_location, [
                        'name' => $location_name,
                        'count_asset_location' => $count_asset_location,
                    ]);
                }

                $results[] = [
                    'id' => $study_program_id,
                    'name' => $name,
                    'count_asset' => $count_asset,
                    'locations' => $results_location
                ];
            }
            //return successful response
            return response()->json(['error' => false, 'result' => $results], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getAllAssetLogSuccess()
    {
        try {
            $query = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date'])->where('status', 'Sukses')->get();
            //return successful response
            return response()->json(['error' => false, 'result' => $query], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getTotalImprovementPriceByQuartalYear()
    {
        try {
            $currentYear = date("Y");

            $queryYear = QuarterYear::where('year', '=', $currentYear)->first();
            $startTw1 = $queryYear['start_tw_1'] ? $queryYear['start_tw_1'] : $currentYear . '01-01';
            $endTw1 = $queryYear['end_tw_1'] ? $queryYear['end_tw_1'] : $currentYear . '04-30';

            $startTw2 = $queryYear['start_tw_2'] ? $queryYear['start_tw_2'] : $currentYear . '05-01';
            $endTw2 = $queryYear['end_tw_2'] ? $queryYear['end_tw_2'] : $currentYear . '08-31';

            $startTw3 = $queryYear['start_tw_3'] ? $queryYear['start_tw_3'] : $currentYear . '09-01';
            $endTw3 = $queryYear['end_tw_3'] ? $queryYear['end_tw_3'] : $currentYear . '12-31';

            $queryTw1 = AssetImprovement::select(['improvement_price', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw1, $endTw1])
                ->whereIn('status', ['Sukses', 'Gagal'])->sum('improvement_price');

            $queryTw2 = AssetImprovement::select(['improvement_price', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw2, $endTw2])
                ->whereIn('status', ['Sukses', 'Gagal'])->sum('improvement_price');

            $queryTw3 = AssetImprovement::select(['improvement_price', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw3, $endTw3])
                ->whereIn('status', ['Sukses', 'Gagal'])->sum('improvement_price');

            $result = [
                'tw_1' => (int)$queryTw1,
                'tw_2' => (int)$queryTw2,
                'tw_3' => (int)$queryTw3
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }

    public function getPercentageStatusByQuartalYear()
    {
        try {
            $currentYear = date("Y");

            $queryYear = QuarterYear::where('year', '=', $currentYear)->first();
            $startTw1 = $queryYear['start_tw_1'] ? $queryYear['start_tw_1'] : $currentYear . '01-01';
            $endTw1 = $queryYear['end_tw_1'] ? $queryYear['end_tw_1'] : $currentYear . '04-30';

            $startTw2 = $queryYear['start_tw_2'] ? $queryYear['start_tw_2'] : $currentYear . '05-01';
            $endTw2 = $queryYear['end_tw_2'] ? $queryYear['end_tw_2'] : $currentYear . '08-31';

            $startTw3 = $queryYear['start_tw_3'] ? $queryYear['start_tw_3'] : $currentYear . '09-01';
            $endTw3 = $queryYear['end_tw_3'] ? $queryYear['end_tw_3'] : $currentYear . '12-31';

            $queryTw1All = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw1, $endTw1])
                ->whereIn('status', ['Sukses', 'Gagal'])->count();

            $queryTw1Sukses = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw1, $endTw1])
                ->whereIn('status', ['Sukses'])->count();

            $queryTw1Gagal = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw1, $endTw1])
                ->whereIn('status', ['Gagal'])->count();

            $queryTw2All = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw2, $endTw2])
                ->whereIn('status', ['Sukses', 'Gagal'])->count();

            $queryTw2Sukses = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw2, $endTw2])
                ->whereIn('status', ['Sukses'])->count();

            $queryTw2Gagal = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw2, $endTw2])
                ->whereIn('status', ['Gagal'])->count();

            $queryTw3All = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw3, $endTw3])
                ->whereIn('status', ['Sukses', 'Gagal'])->count();

            $queryTw3Sukses = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw3, $endTw3])
                ->whereIn('status', ['Sukses'])->count();

            $queryTw3Gagal = AssetImprovement::select(['status', 'actual_repair_start_date', 'actual_repair_end_date', 'created_at'])
                ->whereBetween('actual_repair_end_date', [$startTw3, $endTw3])
                ->whereIn('status', ['Gagal'])->count();

            $result = [
                'tw_1' => [
                    'all' => (int)$queryTw1All,
                    'success' => (int)$queryTw1Sukses,
                    'failed' => (int)$queryTw1Gagal
                ],
                'tw_2' => [
                    'all' => (int)$queryTw2All,
                    'success' => (int)$queryTw2Sukses,
                    'failed' => (int)$queryTw2Gagal
                ],
                'tw_3' => [
                    'all' => (int)$queryTw3All,
                    'success' => (int)$queryTw3Sukses,
                    'failed' => (int)$queryTw3Gagal
                ]
            ];
            //return successful response
            return response()->json(['error' => false, 'result' => $result], 200);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['error' => true, 'message' => $e->getMessage()], 406);
        }
    }
}
