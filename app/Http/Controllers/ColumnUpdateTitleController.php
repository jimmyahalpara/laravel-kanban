<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ColumnUpdateTitleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Column $column, Request $request): RedirectResponse
    {
        $request -> validate([
            'title' => 'required|string|max:255',
        ]);
        $column->title = $request->title;
        $column->save();
        return back();
    }
}
