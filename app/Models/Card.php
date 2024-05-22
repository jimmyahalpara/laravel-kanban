<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'column_id',
        'position',
        'title',
        'is_completed',
        'deadline',
        'parent_id',
        'total_quantity',
        'completed_quantity',
        'card_category_id',
    ];

    // casts 
    protected $casts = [
        'is_completed' => 'boolean',
        'deadline' => 'datetime',
    ];

    // // hidden created_at and updated_at
    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    // ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Card::class, 'parent_id') -> orderBy('position');
    }

    public function cardCategory(): BelongsTo
    {
        return $this->belongsTo(CardCategory::class);
    }
}
