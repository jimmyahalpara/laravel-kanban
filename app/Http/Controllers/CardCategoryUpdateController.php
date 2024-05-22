<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\CardCategory;
use Illuminate\Http\Request;

class CardCategoryUpdateController extends Controller
{
    public function __invoke(Request $request, Board $board, CardCategory $cardCategory)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $cardCategory -> update([
            'name' => $request->name,
            'color' => $request->color ?? '#000000',
            'background_color' => $request->background_color ?? '#ffffff',
        ]);
        return back();
    }
}
