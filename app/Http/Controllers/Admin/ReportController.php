<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class ReportController extends Controller
{
    public function index()
    {
        $adminId = auth()->id();

        $report = Result::select(
            DB::raw('SUM(total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(win_amount) as total_win_amount'),
            DB::raw('SUM(net_win) as total_net_win'),
            DB::raw('COUNT(*) as total_games'),
            'players.name as player_name',
            'agents.name as agent_name',
            'players.id as user_id'
        )
            ->join('users as players', 'results.user_id', '=', 'players.id')
            ->join('users as agents', 'players.agent_id', '=', 'agents.id')
            ->groupBy('players.name', 'agents.name', 'players.id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('report'));
    }

    public function getReportDetails($player_id)
    {
        $details = Result::where('user_id', $player_id)
            ->join('users', 'results.user_id', '=', 'users.id')
            ->select('results.*', 'users.name as user_name')
            ->get();

        // Calculate totals
        $totalBet = $details->sum('total_bet_amount');
        $totalWin = $details->sum('win_amount');
        $totalNetWin = $details->sum('net_win');

        return view('admin.reports.detail', compact('details', 'totalBet', 'totalWin', 'totalNetWin'));
    }

    public function Reportindex()
    {
        $adminId = auth()->id(); // Get the authenticated admin's ID

        $report = Result::select(
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.win_amount) as total_win_amount'),
            DB::raw('SUM(results.net_win) as total_net_win'),
            DB::raw('COUNT(results.id) as total_games'),
            'players.name as player_name',
            'agents.name as agent_name',
            'players.id as user_id'
        )
            ->join('users as players', 'results.user_id', '=', 'players.id') // Join players with results
            ->join('users as agents', 'players.agent_id', '=', 'agents.id') // Join agents with players
            ->where('agents.agent_id', $adminId) // Filter agents belonging to the authenticated admin
            ->groupBy('players.name', 'agents.name', 'players.id') // Group by player name, agent name, and player ID
            ->paginate(10) // Paginate results to show 10 per page
            ->withQueryString(); // Preserve query string in pagination links

        // Calculate totals
        $totalBet = $report->sum('total_bet_amount');
        $totalWin = $report->sum('total_win_amount');
        $totalNetWin = $report->sum('total_net_win');

        return view('admin.reports.index_report', compact('report', 'totalBet', 'totalWin', 'totalNetWin'));
    }

    public function playerDetails($playerId)
    {
        $details = Result::where('user_id', $playerId)
            ->join('users', 'results.user_id', '=', 'users.id')
            ->select('results.*', 'users.name as user_name')
            ->get();
        //->paginate(10); // Paginate results to show 10 per page
        // Calculate totals
        $totalBet = $details->sum('total_bet_amount');
        $totalWin = $details->sum('win_amount');
        $totalNetWin = $details->sum('net_win');

        return view('admin.reports.agent_player_details', compact('details', 'totalBet', 'totalWin', 'totalNetWin'));
    }

    public function AgentReportindex()
    {
        $agentId = auth()->id(); // Get the authenticated agent's ID

        $report = Result::select(
            DB::raw('SUM(results.total_bet_amount) as total_bet_amount'),
            DB::raw('SUM(results.win_amount) as total_win_amount'),
            DB::raw('SUM(results.net_win) as total_net_win'),
            DB::raw('COUNT(results.id) as total_games'),
            'players.name as player_name',
            'agents.name as agent_name',
            'players.id as user_id'
        )
            ->join('users as players', 'results.user_id', '=', 'players.id') // Join players with results
            ->join('users as agents', 'players.agent_id', '=', 'agents.id') // Join agents with players
            ->where('agents.id', $agentId) // Filter data for the authenticated agent only
            ->groupBy('players.name', 'agents.name', 'players.id') // Group by player name, agent name, and player ID
            ->paginate(10) // Paginate results to show 10 per page
            ->withQueryString(); // Preserve query string in pagination links

        // Calculate totals
        $totalBet = $report->sum('total_bet_amount');
        $totalWin = $report->sum('total_win_amount');
        $totalNetWin = $report->sum('total_net_win');

        return view('admin.reports.agent_index_report', compact('report', 'totalBet', 'totalWin', 'totalNetWin'));
    }

    public function getTransactionDetails($tranId)
    {
        $operatorId = 'delightMMK';

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

    public function getResultsForUser($userName)
    {
        // Fetch results data for the given user_name
        $results = DB::table('results')
            ->join('users', 'results.user_id', '=', 'users.id') // Join results with users table
            ->where('users.user_name', $userName) // Filter by user_name
            ->select(
                'results.id',
                'results.player_name',
                'results.game_provide_name',
                'results.game_name',
                'results.operator_id',
                'results.request_date_time',
                'results.signature',
                'results.player_id',
                'results.currency',
                'results.round_id',
                'results.bet_ids',
                'results.result_id',
                'results.game_code',
                'results.total_bet_amount',
                'results.win_amount',
                'results.net_win',
                'results.tran_date_time',
                'users.name as user_name'
            )
            ->get();

        // Return the data as JSON
        return response()->json($results);
    }

    public function getResultsForOnlyUser($userName)
    {
        // Fetch results data for the given user_name
        $results = DB::table('results')
            ->join('users', 'results.user_id', '=', 'users.id') // Join results with users table
            ->where('users.user_name', $userName) // Filter by user_name
            ->select(
                'results.id',
                'results.player_name',
                'results.game_provide_name',
                'results.game_name',
                'results.operator_id',
                'results.request_date_time',
                'results.signature',
                'results.player_id',
                'results.currency',
                'results.round_id',
                'results.bet_ids',
                'results.result_id',
                'results.game_code',
                'results.total_bet_amount',
                'results.win_amount',
                'results.net_win',
                'results.tran_date_time',
                'users.name as user_name'
            )
            ->get();

        // Return the data as JSON
        return view('admin.reports.v3_index', compact('results'));
    }

    public function GetResult()
    {
        return view('admin.reports.find_by_username_index');
    }

    public function FindByUserName(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'user_name' => 'required|string|exists:users,user_name', // Ensure 'user_name' is provided and exists in the 'users' table
        ]);

        // Fetch the user_name from the request
        $userName = $request->input('user_name');

        // Fetch results data for the given user_name
        $results = DB::table('results')
            ->join('users', 'results.user_id', '=', 'users.id') // Join results with users table
            ->where('users.user_name', $userName) // Filter by user_name
            ->select(
                'results.id',
                'results.player_name',
                'results.game_provide_name',
                'results.game_name',
                'results.operator_id',
                'results.request_date_time',
                'results.signature',
                'results.player_id',
                'results.currency',
                'results.round_id',
                'results.bet_ids',
                'results.result_id',
                'results.game_code',
                'results.total_bet_amount',
                'results.win_amount',
                'results.net_win',
                'results.tran_date_time',
                'users.name as user_name'
            )
            ->get();

        // Return the data to the view
        return view('admin.reports.find_by_username_index', compact('results'));
    }


public function BoReport()
{
    $response = Http::post('https://agdashboard.pro/proxy-to-bo/', [
    'username' => 'delightMMK',
    'password' => '123456',
]);

if ($response->ok()) {
    return $response->body(); // Display the response from the backend
} else {
    return 'Error: Unable to login';
}
}

}