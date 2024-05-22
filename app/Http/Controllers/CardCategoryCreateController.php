<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class CardCategoryCreateController extends Controller
{
    public function __invoke(Request $request, Board $board)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $board->cardCategories()->create([
            'name' => $request->name,
            'color' => $request->color ?? '#000000',
            'background_color' => $request->background_color ?? '#ffffff',
        ]);

        return back();
    }
}
