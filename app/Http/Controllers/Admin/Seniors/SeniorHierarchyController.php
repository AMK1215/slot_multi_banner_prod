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
    public function GetSeniorHierarchy()
    {
        $senior_id = Auth::id(); // Authenticated user ID

        // Fetch the authenticated user
        $senior = User::with([
            'children' => function ($query) {
                // Fetch owners with their child agents
                $query->with([
                    'children' => function ($query) {
                        // Fetch agents with their child players
                        $query->with('children');
                    },
                ]);
            },
        ])->find($senior_id);

        // Check if the user exists and has the 'Senior' role
        if (! $senior || ! $senior->hasRole('Senior')) {
            return redirect()->back()->with('error', 'You are not authorized to view this hierarchy information.');
        }

        return view('admin.senior_info.index', ['senior' => $senior]);
    }
}
