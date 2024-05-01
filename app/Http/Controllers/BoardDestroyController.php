<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardDestroyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, Board $board) {
        $board->delete();

        $lastBoard = $request->user()->boards()->latest()->first();
        if ($lastBoard){
            return redirect()->route('boards', $lastBoard);
        } else {
            return redirect()->route('dashboard');
        }
    }
}
