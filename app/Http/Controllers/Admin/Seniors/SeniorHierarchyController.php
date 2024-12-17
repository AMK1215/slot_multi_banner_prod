<?php

namespace App\Http\Controllers\Admin\Seniors;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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
    // public function GetSeniorHierarchy()
    // {
    //     $senior_id = Auth::id(); // Authenticated user ID

    //     // Fetch the authenticated user with hierarchical children
    //     $senior = User::with([
    //         'children' => function ($query) {
    //             $query->with([
    //                 'children' => function ($query) {
    //                     $query->with('children');
    //                 },
    //             ]);
    //         },
    //     ])->find($senior_id);

    //     // Check if the user exists and has the 'Senior' role
    //     if (!$senior || !$senior->hasRole('Senior')) {
    //         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
    //     }

    //     // Group the data by Owner's Name and Agent's Name
    //     $groupedData = $senior->children->map(function ($owner) {
    //         return [
    //             'owner_name' => $owner->name,
    //             'owner_balance' => $owner->wallet->balanceFloat ?? '0.00',
    //             'agents' => $owner->children->groupBy('name')->map(function ($agents) {
    //                 return $agents->map(function ($agent) {
    //                     return [
    //                         'agent_name' => $agent->name,
    //                         'agent_balance' => $agent->wallet->balanceFloat ?? '0.00',
    //                         'players' => $agent->children->map(function ($player) {
    //                             return [
    //                                 'player_name' => $player->name,
    //                                 'player_balance' => $player->wallet->balanceFloat ?? '0.00',
    //                             ];
    //                         }),
    //                     ];
    //                 });
    //             }),
    //         ];
    //     });

    //     return view('admin.senior_info.index', [
    //         'groupedData' => $groupedData,
    //     ]);
    // }

//     public function GetSeniorHierarchy(Request $request)
// {
//     $senior_id = Auth::id(); // Authenticated user ID

//     // Fetch the authenticated user with hierarchical children
//     $senior = User::with([
//         'children' => function ($query) {
//             $query->with([
//                 'children' => function ($query) {
//                     $query->with('children');
//                 },
//             ]);
//         },
//     ])->find($senior_id);

//     // Check if the user exists and has the 'Senior' role
//     if (!$senior || !$senior->hasRole('Senior')) {
//         return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
//     }

//     // Group the data by Owner's Name and Agent's Name
//     $groupedData = $senior->children->map(function ($owner) {
//         return [
//             'owner_name' => $owner->name,
//             'owner_balance' => $owner->wallet->balanceFloat ?? '0.00',
//             'agents' => $owner->children->groupBy('name')->map(function ($agents) {
//                 return $agents->map(function ($agent) {
//                     return [
//                         'agent_name' => $agent->name,
//                         'agent_balance' => $agent->wallet->balanceFloat ?? '0.00',
//                         'players' => $agent->children->map(function ($player) {
//                             return [
//                                 'player_name' => $player->name,
//                                 'player_balance' => $player->wallet->balanceFloat ?? '0.00',
//                             ];
//                         }),
//                     ];
//                 });
//             }),
//         ];
//     });

//     // Flatten the data for pagination
//     $flattenedData = collect();
//     foreach ($groupedData as $owner) {
//         foreach ($owner['agents'] as $agentsGroup) {
//             foreach ($agentsGroup as $agent) {
//                 foreach ($agent['players'] as $player) {
//                     $flattenedData->push([
//                         'owner_name' => $owner['owner_name'],
//                         'owner_balance' => $owner['owner_balance'],
//                         'agent_name' => $agent['agent_name'],
//                         'agent_balance' => $agent['agent_balance'],
//                         'player_name' => $player['player_name'],
//                         'player_balance' => $player['player_balance'],
//                     ]);
//                 }
//             }
//         }
//     }

//     // Manual Pagination
//     $currentPage = LengthAwarePaginator::resolveCurrentPage();
//     $perPage = 10; // Number of records per page
//     $currentItems = $flattenedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

//     $paginatedData = new LengthAwarePaginator(
//         $currentItems, 
//         $flattenedData->count(), 
//         $perPage, 
//         $currentPage, 
//         ['path' => $request->url(), 'query' => $request->query()]
//     );

//     return view('admin.senior_info.index', [
//         'groupedData' => $paginatedData,
//     ]);
// }

    
public function GetSeniorHierarchy(Request $request) 
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

    // Variables to store total balances
    $totalOwnerBalance = 0;
    $totalAgentBalance = 0;
    $totalPlayerBalance = 0;

    // Group the data by Owner's Name and Agent's Name
    $groupedData = $senior->children->map(function ($owner) use (&$totalOwnerBalance, &$totalAgentBalance, &$totalPlayerBalance) {
        // Add Owner balance
        $ownerBalance = $owner->wallet->balanceFloat ?? 0.00;
        $totalOwnerBalance += $ownerBalance;

        return [
            'owner_name' => $owner->name,
            'owner_balance' => $ownerBalance,
            'agents' => $owner->children->groupBy('name')->map(function ($agents) use (&$totalAgentBalance, &$totalPlayerBalance) {
                return $agents->map(function ($agent) use (&$totalAgentBalance, &$totalPlayerBalance) {
                    // Add Agent balance
                    $agentBalance = $agent->wallet->balanceFloat ?? 0.00;
                    $totalAgentBalance += $agentBalance;

                    return [
                        'agent_name' => $agent->name,
                        'agent_balance' => $agentBalance,
                        'players' => $agent->children->map(function ($player) use (&$totalPlayerBalance) {
                            // Add Player balance
                            $playerBalance = $player->wallet->balanceFloat ?? 0.00;
                            $totalPlayerBalance += $playerBalance;

                            return [
                                'player_name' => $player->name,
                                'player_balance' => $playerBalance,
                            ];
                        }),
                    ];
                });
            }),
        ];
    });

    // Flatten the data for pagination
    $flattenedData = collect();
    foreach ($groupedData as $owner) {
        foreach ($owner['agents'] as $agentsGroup) {
            foreach ($agentsGroup as $agent) {
                foreach ($agent['players'] as $player) {
                    $flattenedData->push([
                        'owner_name' => $owner['owner_name'],
                        'owner_balance' => $owner['owner_balance'],
                        'agent_name' => $agent['agent_name'],
                        'agent_balance' => $agent['agent_balance'],
                        'player_name' => $player['player_name'],
                        'player_balance' => $player['player_balance'],
                    ]);
                }
            }
        }
    }

    // Manual Pagination
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $perPage = 10; // Number of records per page
    $currentItems = $flattenedData->slice(($currentPage - 1) * $perPage, $perPage)->all();

    $paginatedData = new LengthAwarePaginator(
        $currentItems, 
        $flattenedData->count(), 
        $perPage, 
        $currentPage, 
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Pass total balances to the view
    return view('admin.senior_info.index', [
        'groupedData' => $paginatedData,
        'totalOwnerBalance' => $totalOwnerBalance,
        'totalAgentBalance' => $totalAgentBalance,
        'totalPlayerBalance' => $totalPlayerBalance,
    ]);
}

}