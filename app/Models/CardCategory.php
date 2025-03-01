<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color', 'background_color', 'board_id'];

    // make name uppercase when saving in boot
    public static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $category->name = strtoupper($category->name);
        });
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function deleteAllCards(){
        $this->cards()->delete();
    }
}
