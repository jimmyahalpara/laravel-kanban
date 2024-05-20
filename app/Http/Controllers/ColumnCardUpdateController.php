<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCardRequest;
use App\Models\Card;
use App\Models\Column;
use Illuminate\Http\RedirectResponse;

class ColumnCardUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateCardRequest $request, Column $column, Card $card): RedirectResponse
    {
        $data = $request->all();
        // remove values where value is null
        $data = array_filter($data, fn ($value) => $value !== null);
        $card->update($request->all());

        // if card is complete, mark all children as complete
        if ($card->is_completed) {
            $card->children->each->update(['is_completed' => true]);
        }
        // if card is incomplete, mark all parents as incomplete
        else {
            $card->parent?->update(['is_completed' => false]);
        }

        // if parents all children are complete, mark parent as complete
        if ($card->parent?->children->every->is_completed) {
            $card->parent->update(['is_completed' => true]);
        }
        // for each children, if deadline is not set, or if more than parent deadline, then set deadline to parent deadline
        $card->children->each(function ($child) use ($card) {
            if ($child->deadline === null || $child->deadline > $card->deadline) {
                $child->update(['deadline' => $card->deadline]);
            }
        });
        return back();
    }
}
