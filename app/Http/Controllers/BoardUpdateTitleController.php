<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardUpdateTitleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, Board $board) {
        $board->update($request->validate([
            'title' => 'required|string|max:255',
        ]));
        $board -> title = $request -> title;
        return redirect()->back();
    }
}
