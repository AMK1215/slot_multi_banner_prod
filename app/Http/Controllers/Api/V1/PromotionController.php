<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Promotion;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    use HttpResponses;

    // public function index()
    // {
    //     $data = Promotion::all();

    //     return $this->success($data);

    // }

    public function index()
    {
        $user = Auth::user();
        //dd($user);

        if (! $user) {
            return $this->error(null, 'Unauthorized', 401);
        }

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = Promotion::where('admin_id', $admin->id)->get();

        return $this->success($data, 'Promotion retrieved successfully.');
    }

    public function show($id)
    {
        $promotion = Promotion::find($id);

        return $this->success($promotion);
    }
}
