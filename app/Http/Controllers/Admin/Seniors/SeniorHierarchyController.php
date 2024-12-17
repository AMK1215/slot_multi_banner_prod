<?php

namespace App\Http\Controllers\Admin\Seniors;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeniorHierarchyController extends Controller
{
    /**
     * Display the hierarchical data for a senior.
     */
    // public function GetSeniorHierarchy()
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             // Fetch owners with their child agents
    //             $query->with([
    //                 'children' => function ($query) {
    //                     // Fetch agents with their child players
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (! $senior || ! $senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     return view('admin.senior_info.index', ['senior' => $senior]);
    // }
    public function GetSeniorHierarchy()
    {
        $senior_id = Auth::id(); // Authenticated user ID

        // Fetch the authenticated user with hierarchical children
        $senior = User::with([
            'children' => function ($query) {
                $query->with([
                    'children' => function ($query) {
                        $query->with('children');
                    },
                ]);
            },
        ])->find($senior_id);

        // Check if the user exists and has the 'Senior' role
        if (!$senior || !$senior->hasRole('Senior')) {
            return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
        }

        // Group the data by Owner's Name and Agent's Name
        $groupedData = $senior->children->map(function ($owner) {
            return [
                'owner_name' => $owner->name,
                'owner_balance' => $owner->wallet->balanceFloat ?? '0.00',
                'agents' => $owner->children->groupBy('name')->map(function ($agents) {
                    return $agents->map(function ($agent) {
                        return [
                            'agent_name' => $agent->name,
                            'agent_balance' => $agent->wallet->balanceFloat ?? '0.00',
                            'players' => $agent->children->map(function ($player) {
                                return [
                                    'player_name' => $player->name,
                                    'player_balance' => $player->wallet->balanceFloat ?? '0.00',
                                ];
                            }),
                        ];
                    });
                }),
            ];
        });

        return view('admin.senior_info.index', [
            'groupedData' => $groupedData,
        ]);
    }
}