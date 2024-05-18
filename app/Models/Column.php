<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Column extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'board_id',
        'order'
    ];

    // onsaving the column, fill order with the last column position, grouped by board_id
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($column) {
            $column->order = static::where('board_id', $column->board_id)->max('order') + 1;
        });
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class)->orderBy('position');
    }

    public function addCard(Card $card): void{
        $this->cards()->save($card);
    }

    /**
     * Move the column to the specified direction by one step by updating order 
     * 
     * @param string $direction left or right 
     * 
     * @return void
     */
    public function move(string $direction){
        $currentOrder = $this->order;
        $newOrder = $direction === 'left' ? $currentOrder - 1 : $currentOrder + 1;

        $columnToSwap = static::where('board_id', $this->board_id)
            ->where('order', $newOrder)
            ->first();

        if($columnToSwap){
            $this->order = $newOrder;
            $columnToSwap->order = $currentOrder;

            $this->save();
            $columnToSwap->save();
        }
    }
}
