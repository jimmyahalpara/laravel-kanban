<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoardStoreController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        $board = $request->user()->boards()->create($request->only('title'));
        return redirect()->route('boards', $board);
    }
}
