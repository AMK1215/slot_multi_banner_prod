<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\GameList;

class GameListImageURLUpdateController extends Controller
{

     /**
     * Display the list of games with images.
     */
    public function index()
    {
        $games = GameList::all(); // Retrieve all game records
        return view('admin.game_list_images', compact('games'));
    }

    /**
     * Update the image_url for a specific game.
     */

    public function edit(GameList $gameList)
    {
        return view('admin.game_list.edit', compact('gameList'));
    }
    public function updateImageUrl(Request $request, $id)
    {
        $game = GameList::findOrFail($id);

        // Validate the request
        $request->validate([
            'image_url' => 'required|url',
        ]);

        // Update the image_url
        $game->update([
            'image_url' => $request->input('image_url'),
        ]);

        return redirect()->route('admin.gameLists.index')->with('success', 'Image URL updated successfully.');
    }

}