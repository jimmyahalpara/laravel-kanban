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
        $cards = Card::orderBy('priority') -> get();
        $cards = CardResource::collection($cards);

        $boards = Board::all();
        // board columns group by board
        $board_columns = Column::all() -> groupBy('board_id');
        
        $board_categories = CardCategory::all() -> groupBy('board_id');

        return Inertia::render('Dashboard', [
            'cards' => $cards,
            // 'boards' => $boards,
            'board_columns' => $board_columns,
            'board_categories' => $board_categories
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
            ]);

            $priority++;
        }
        return redirect() -> back() -> with('success', 'Cards updated successfully');
    }
}
