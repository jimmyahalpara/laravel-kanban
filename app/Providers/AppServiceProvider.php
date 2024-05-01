<?php

namespace App\Providers;

use App\Models\Card;
use Inertia\Inertia;
use App\Models\Column;
use App\Observers\CardObserver;
use App\Observers\ColumnObserver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observe deleting a column to delete all related cards
        Column::observe(ColumnObserver::class);

        // Observe adding a new card to increment the position accordingly
        Card::observe(CardObserver::class);

        // Make every nested array a collection, easier to work with
        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });

        Schema::defaultStringLength(191);


        Inertia::share([
            'boards' => fn () => auth()->user()->boards->map->only('id', 'title'),
        ]);

    }
}
