<?php

namespace App\Http\Controllers\Api\V1\Monitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Webhook\Bet;
use App\Models\Webhook\Result;

use App\Traits\HttpResponses;


class DataVisualizationController extends Controller
{
    use HttpResponses;

    public function VisualizeBet()
    {
        try {
            $bets = Bet::all();
            return $this->success($bets, 'Bets retrieved successfully.');
        } catch (\Exception $e) {
            return $this->error(null, 'Failed to retrieve bets.', 500);
        }
    }

     public function VisualizeResult()
    {
        try {
            $results = Result::all();
            return $this->success($results, 'Results retrieved successfully.');
        } catch (\Exception $e) {
            return $this->error(null, 'Failed to retrieve results.', 500);
        }
    }
}