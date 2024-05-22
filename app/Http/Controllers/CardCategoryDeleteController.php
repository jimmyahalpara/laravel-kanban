<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\CardCategory;
use Illuminate\Http\Request;

class CardCategoryDeleteController extends Controller
{
    public function __invoke(Request $request, Board $board, CardCategory $cardCategory)
    {
        $cardCategory->delete();
        return back();
    }
}
