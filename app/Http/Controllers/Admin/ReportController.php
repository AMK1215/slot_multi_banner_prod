<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ReportController extends Controller
{
    // public function getReportGroupedByGameProvider()
    // {
    //     $report = Result::select(
    //         'game_provide_name',
    //         DB::raw('SUM(total_bet_amount) as total_bet_amount'),
    //         DB::raw('SUM(win_amount) as total_win_amount'),
    //         DB::raw('SUM(net_win) as total_net_win'),
    //         DB::raw('COUNT(*) as total_games'),
    //         'users.name as user_name'
    //     )
    //         ->join('users', 'results.user_id', '=', 'users.id')  // Join with the users table
    //         ->groupBy('game_provide_name', 'users.name')  // Group by both game provider and user name
    //         ->get();

    //     //return $report;
    //     return view('admin.reports.index', compact('report'));
    // }

    public function getReportGroupedByGameProvider()
{
    // Get the authenticated admin's ID
    $adminId = auth()->id();

    // Get the agents associated with this admin
    $agents = User::where('agent_id', $adminId)->pluck('id');

    // Get players associated with those agents
    $players = User::whereIn('agent_id', $agents)->pluck('id');

    // Fetch the report grouped by game provider and player
    $report = Result::select(
        'game_provide_name',
        DB::raw('SUM(total_bet_amount) as total_bet_amount'),
        DB::raw('SUM(win_amount) as total_win_amount'),
        DB::raw('SUM(net_win) as total_net_win'),
        DB::raw('COUNT(*) as total_games'),
        'users.name as user_name'
    )
        ->join('users', 'results.user_id', '=', 'users.id') // Join with the users table
        ->whereIn('results.user_id', $players) // Filter by the players under this admin
        ->groupBy('game_provide_name', 'users.name') // Group by both game provider and user name
        ->get();

    // Return the view with the report
    return view('admin.reports.index', compact('report'));
}


    public function getReportDetails($game_provide_name)
    {
        $details = Result::where('game_provide_name', $game_provide_name)
            ->join('users', 'results.user_id', '=', 'users.id')
            ->select('results.*', 'users.name as user_name')
            ->get();

        return view('admin.reports.detail', compact('details', 'game_provide_name'));
    }

    public function getTransactionDetails($tranId)
    {
        $operatorId = 'delightMMK';

        //$url = 'https://api.sm-sspi-uat.com/api/opgateway/v1/op/GetTransactionDetails';
        $url = 'https://api.sm-sspi-prod.com/api/opgateway/v1/op/GetTransactionDetails';

        // Generate the RequestDateTime in UTC
        $requestDateTime = Carbon::now('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $secretKey = '1OMJXOf88RHKpcuT';
        $functionName = 'GetTransactionDetails';
        $signatureString = $functionName.$requestDateTime.$operatorId.$secretKey;
        $signature = md5($signatureString);

        // Prepare request payload
        $payload = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'TranId' => $tranId,
        ];

        try {
            // Make the POST request to the API endpoint
            $response = Http::post($url, $payload);

            // Check if the response is successful
            if ($response->successful()) {
                return $response->json(); // Return the response data as JSON
            } else {
                Log::error('Failed to get transaction details', ['response' => $response->body()]);

                return response()->json(['error' => 'Failed to get transaction details'], 500);
            }
        } catch (\Exception $e) {
            Log::error('API request error', ['message' => $e->getMessage()]);

            return response()->json(['error' => 'API request error'], 500);
        }
    }

    //     public function getTransactionDetails($operatorId, $tranId)
    // {
    //     $url = 'https://api.sm-sspi-uat.com/api/opgateway/v1/op/GetTransactionDetails'; // Replace with the actual URL

    //     // Generate the RequestDateTime in UTC
    //     $requestDateTime = Carbon::now('UTC')->format('Y-m-d H:i:s');

    //     // Generate the signature using MD5 hashing
    //     $secretKey = 's4fZpFsRfGp3VMeG'; // Replace with your actual secret key
    //     $functionName = 'GetTransactionDetails';
    //     $signatureString = $functionName . $requestDateTime . $operatorId . $secretKey;
    //     $signature = md5($signatureString);

    //     // Prepare request payload
    //     $payload = [
    //         'OperatorId' => $operatorId,
    //         'RequestDateTime' => $requestDateTime,
    //         'Signature' => $signature,
    //         'TranId' => $tranId
    //     ];

    //     try {
    //         // Make the POST request to the API endpoint
    //         $response = Http::post($url, $payload);

    //         // Check if the response is successful
    //         if ($response->successful()) {
    //             return $response->json(); // Return the response data as JSON
    //         } else {
    //             Log::error('Failed to get transaction details', ['response' => $response->body()]);
    //             return response()->json(['error' => 'Failed to get transaction details'], 500);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('API request error', ['message' => $e->getMessage()]);
    //         return response()->json(['error' => 'API request error'], 500);
    //     }
    // }

}

/*
use Illuminate\Support\Facades\DB;

public function getReportGroupedByGameProvider()
{
    $report = DB::select(DB::raw("
        SELECT results.*, users.name as user_name,
               SUM(total_bet_amount) as total_bet_amount,
               SUM(win_amount) as total_win_amount,
               SUM(net_win) as total_net_win,
               COUNT(*) as total_games
        FROM results
        JOIN users ON results.user_id = users.id
        GROUP BY game_provide_name, user_name
    "));

    return view('admin.reports.index', compact('report'));
}
**/