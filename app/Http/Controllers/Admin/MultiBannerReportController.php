<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Webhook\Result;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MultiBannerReportController extends Controller
{
    protected const SUB_AGENT_ROlE = 'Sub Agent';

    public function getSeniorReport()
    {
        $seniorId = auth()->id();

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $admins = User::with([
            'agents.players.results' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'agents.players.betNResults' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
            ->where('agent_id', $seniorId)
            ->whereHas('agents.players.results', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orWhereHas('agents.players.betNResults', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        $data = [];
        foreach ($admins as $admin) {
            $totalBets = 0;
            $totalWins = 0;
            $totalNet = 0;

            foreach ($admin->agents as $agent) {
                foreach ($agent->players as $player) {

                    $totalBets += $player->results->sum('total_bet_amount') + $player->betNResults->sum('bet_amount');
                    $totalWins += $player->results->sum('win_amount') + $player->betNResults->sum('win_amount');
                    $totalNet += $player->results->sum('net_win') + $player->betNResults->sum('net_win');
                }
            }

            $data[] = [
                'admin_name' => $agent->name,
                'total_bets' => $totalBets,
                'total_wins' => $totalWins,
                'total_net' => $totalNet,
            ];
        }

        return view('admin.reports.senior.index', compact('data', 'admins'));
    }

    public function getOwnerReport()
    {
        $ownerId = auth()->id();

        $startDate = $request->start_date ?? Carbon::today()->startOfDay()->toDateString();
        $endDate = $request->end_date ?? Carbon::today()->endOfDay()->toDateString();

        $agents = User::with([
            'agents.players.results' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'agents.players.betNResults' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
            ->where('agent_id', $ownerId)
            ->whereHas('agents.players.results', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orWhereHas('agents.players.betNResults', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        $data = [];
        
        foreach ($agents as $agent) {
            $totalBets = 0;
            $totalWins = 0;
            $totalNet = 0;

            foreach ($agent->agents as $player) {
                $totalBets += $player->results->sum('total_bet_amount') + $player->betNResults->sum('bet_amount');
                $totalWins += $player->results->sum('win_amount') + $player->betNResults->sum('win_amount');
                $totalNet += $player->results->sum('net_win') + $player->betNResults->sum('net_win');
            }

            $data[] = [
                'agent_name' => $agent->name,
                'total_bets' => $totalBets,
                'total_wins' => $totalWins,
                'total_net' => $totalNet,
            ];
        }

        return view('admin.reports.owner.index', compact('data'));
    }

    public function getAgentReport()
    {
        $agent = $this->getAgent() ?? Auth::user();

        $players = User::where('agent_id', $agent->id)->get();

        $results = Result::whereIn('user_id', $players->pluck('id'))
            ->selectRaw('user_id, SUM(total_bet_amount) as total_bets, SUM(win_amount) as total_wins, SUM(net_win) as total_net')
            ->groupBy('user_id')
            ->get();

        return view('admin.reports.agent.index', compact('results'));
    }

    public function getAgentDetail($userId)
    {
        $player = User::findOrFail($userId);

        $details = Result::where('user_id', $userId)->get();

        return view('admin.reports.agent.detail', compact('player', 'details'));
    }

    private function isExistingAgent($userId)
    {
        $user = User::find($userId);

        return $user && $user->hasRole(self::SUB_AGENT_ROlE) ? $user->parent : null;
    }

    private function getAgent()
    {
        return $this->isExistingAgent(Auth::id());
    }
}
