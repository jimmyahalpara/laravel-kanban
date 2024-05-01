<?php 

namespace App\Traits;

use App\Models\Scopes\UserScope;


trait OwnedByUserTrait
{
    public static function bootOwnedByUserTrait()
    {
        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });

        static::addGlobalScope(new UserScope);
    }


}