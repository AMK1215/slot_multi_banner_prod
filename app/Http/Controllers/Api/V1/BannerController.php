<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $user = Auth::user();

        // Determine the admin whose banners to fetch
        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = Banner::where('admin_id', $admin->id)->get();

        return $this->success($data, 'Banners retrieved successfully.');
    }

    public function bannerText()
    {
        $user = Auth::user();

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        $data = BannerText::where('admin_id', $admin->id)->get();

        return $this->success($data, 'BannerTexts retrieved successfully.');
    }

    public function AdsBannerIndex()
    {
        $user = Auth::user();
        //dd($user);

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = BannerAds::where('admin_id', $admin->id)->get();

        return $this->success($data, 'BannerAds retrieved successfully.');
    }

    public function AdsBannerTest()
    {
        $user = Auth::user();
        //dd($user);

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        // Fetch banners for the determined admin
        $data = BannerAds::where('admin_id', $admin->id)->latest()->first();

        return $this->success($data, 'BannerAds retrieved successfully.');
    }

    public function bannerTest()
    {
        $user = Auth::user();

        if ($user->parent) {
            // If the user has a parent (Agent or Player), go up the hierarchy
            $admin = $user->parent->parent ?? $user->parent;
        } else {
            // If the user is an Admin, they own the banners
            $admin = $user;
        }

        $data = BannerText::where('admin_id', $admin->id)->latest()->first();

        return $this->success($data, 'BannerTexts retrieved successfully.');
    }
}
