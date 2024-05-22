<?php

namespace App\Models;

use App\Traits\OwnedByUserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasFactory, OwnedByUserTrait;

    protected $fillable = [
        'title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->oldest();
    }

    public function orderedColumns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('order');
    }

    public function addColumn(Column $column): void
    {
        $this->columns()->save($column);
    }

    public function resetColumnOrder(): void
    {
        echo 'Resetting column order for board ' . $this->title . PHP_EOL;
        $this->columns->each(function ($column, $index) {
            echo $column->title . ' ' . $index . PHP_EOL;
            $column->order = $index;
            $column->save();
        });
    }

    public function cardCategories(): HasMany
    {
        return $this->hasMany(CardCategory::class);
    }
}
