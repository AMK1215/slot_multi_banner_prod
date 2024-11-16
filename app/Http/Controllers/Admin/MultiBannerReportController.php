<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Webhook\Result;

class MultiBannerReportController extends Controller
{
    public function getSeniorReport()
{
    $seniorId = auth()->id();

    // Get all Admins under this Senior
    $admins = User::where('agent_id', $seniorId)->get();

    // Aggregate data for each Admin
    $data = [];
    foreach ($admins as $admin) {
        $agents = User::where('agent_id', $admin->id)->get();

        foreach ($agents as $agent) {
            $players = User::where('agent_id', $agent->id)->get();

            $results = Result::whereIn('user_id', $players->pluck('id'))
                ->selectRaw('SUM(total_bet_amount) as total_bets, SUM(win_amount) as total_wins, SUM(net_win) as total_net')
                ->first();

            $data[] = [
                'admin_name' => $admin->name,
                'agent_name' => $agent->name,
                'total_bets' => $results->total_bets ?? 0,
                'total_wins' => $results->total_wins ?? 0,
                'total_net' => $results->total_net ?? 0,
            ];
        }
    }

    return view('admin.reports.senior.index', compact('data'));
}

}