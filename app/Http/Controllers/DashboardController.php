<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Inertia\Inertia;
use App\Models\Board;
use App\Models\Column;
use Illuminate\Http\Request;
use App\Http\Resources\CardResource;
use App\Models\CardCategory;

class DashboardController extends Controller
{
    public function index(Request $request){
        $cards = Card::orderBy('priority');
        if ($request -> input('is_archived') == 1){
            $cards = $cards -> where('is_archived', true);
        } else {
            $cards = $cards -> where('is_archived', false);
        }
        $cards = $cards -> get();
        $cards = CardResource::collection($cards);
        // board columns group by board
        $board_columns = Column::all() -> groupBy('board_id');
        
        $board_categories = CardCategory::all() -> groupBy('board_id');

        return Inertia::render('Dashboard', [
            'cards' => $cards,
            'board_columns' => $board_columns,
            'board_categories' => $board_categories,
            'is_archived' => $request -> input('is_archived') == 1
        ]);
    }

    public function updateCards(Request $request){
        $cards = $request->cards;
        $priority = 0;
        foreach($cards as $card){
            $card_modal = Card::find($card['id']);
            if (!$card) {
                continue;
            }
            $card_modal -> update([
                'title' => $card['title'],
                'priority' => $priority,
                'column_id' => $card['column'],
                'deadline' => $card['deadline'],
                'is_completed' => $card['is_completed'],
                'card_category_id' => $card['card_category_id'],
                'is_archived' => $card['is_archived']
            ]);

            $priority++;
        }
        return redirect() -> back() -> with('success', 'Cards updated successfully');
    }
}
