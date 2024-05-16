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
        return back();
    }
}
