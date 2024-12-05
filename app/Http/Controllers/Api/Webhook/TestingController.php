<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class TestingController extends Controller
{
    public function AppGetGameList(Request $request)
{
    try {
        // Validate the request input
        $request->validate([
            'balance' => 'required|numeric',
        ]);

        // Find the user by their user_name
        $user = \App\Models\User::where('user_name', 'P8704485')->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Locate the user's wallet
        $wallet = \Bavix\Wallet\Models\Wallet::where('holder_type', \App\Models\User::class)
            ->where('holder_id', $user->id)
            ->first();

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found for the user.'], 404);
        }

        // Deposit into the wallet
        app(WalletService::class)->deposit($user, $request->balance, TransactionName::JackPot);

        return response()->json(['success' => 'Balance updated successfully.'], 200);

    } catch (\Exception $e) {
        // Catch any error7s and return a server error response
        return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

}