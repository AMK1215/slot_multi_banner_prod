<?php

namespace App\Http\Controllers\Api\V1\Monitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Webhook\Bet;
use App\Models\Webhook\Result;

use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Log; // Import Log facade

class DataVisualizationController extends Controller
{
    use HttpResponses;

   public function VisualizeBet()
{
    Log::info('VisualizeBet API called.');

    try {
        Log::info('Fetching Bets...');
        $bets = Bet::all();
        Log::info('Bets retrieved successfully.', ['count' => $bets->count()]);
        return $this->success($bets, 'Bets retrieved successfully.');
    } catch (\Exception $e) {
        Log::error('Error in VisualizeBet:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return $this->error(null, 'Failed to retrieve bets.', 500);
    }
}

public function VisualizeResult()
{
    Log::info('VisualizeResult API called.');

    try {
        Log::info('Fetching Results...');
        $results = Result::all();
        Log::info('Results retrieved successfully.', ['count' => $results->count()]);
        return $this->success($results, 'Results retrieved successfully.');
    } catch (\Exception $e) {
        Log::error('Error in VisualizeResult:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return $this->error(null, 'Failed to retrieve results.', 500);
    }
}

}