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
            $bets = Bet::all();
            Log::debug('Retrieved Bets Data:', ['bets' => $bets]); // Debug log
            return $this->success($bets, 'Bets retrieved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Bets.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]); // Error log with stack trace
            return $this->error(null, 'Failed to retrieve bets.', 500);
        }
    }

    public function VisualizeResult()
    {
        Log::info('VisualizeResult API called.');

        try {
            $results = Result::all();
            Log::debug('Retrieved Results Data:', ['results' => $results]); // Debug log
            return $this->success($results, 'Results retrieved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Results.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]); // Error log with stack trace
            return $this->error(null, 'Failed to retrieve results.', 500);
        }
    }
}