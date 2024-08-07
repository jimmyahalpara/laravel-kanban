    <?php

    use App\Http\Controllers\ColumnCardUpdateController;
    use App\Http\Controllers\ColumnCardDestroyController;
    use App\Http\Controllers\ColumnCardCreateController;
    use App\Http\Controllers\CardsReorderUpdateController;
    use App\Http\Controllers\ProfileController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoardStoreController;
use App\Http\Controllers\ColumnMoveController;
use App\Http\Controllers\ColumnSortController;
use App\Http\Controllers\BoardDestroyController;
use App\Http\Controllers\ColumnDestroyController;
use App\Http\Controllers\BoardUpdateTitleController;
use App\Http\Controllers\BoardColumnCreateController;
use App\Http\Controllers\ColumnUpdateTitleController;
use App\Http\Controllers\CardCategoryCreateController;
use App\Http\Controllers\CardCategoryDeleteController;
use App\Http\Controllers\CardCategoryUpdateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/dashboard', function () {
    //     return Inertia::render('Dashboard');
    // })->name('dashboard');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-cards', [DashboardController::class, 'updateCards'])->name('dashboard.update-cards');

    Route::get('/boards/{board?}', [BoardController::class, 'show'])->name('boards');
    Route::post('/boards', BoardStoreController::class)->name('boards.store');
    Route::post('/boards/{board}/update-title', BoardUpdateTitleController::class)->name('boards.update.title');
    Route::delete('/boards/{board}', BoardDestroyController::class)->name('boards.destroy');

    Route::post('/boards/{board}/columns', BoardColumnCreateController::class)
        ->name('boards.columns.store');

    Route::post('/columns/{column}/update-title', ColumnUpdateTitleController::class)
        ->name('columns.update.title');
    Route::delete('/columns/{column}', ColumnDestroyController::class)
        ->name('columns.destroy');
    Route::post('/columns/{column}/move', ColumnMoveController::class)
        ->name('columns.move');
    Route::post('/columns/{column}/sort', ColumnSortController::class)
        ->name('columns.sort');

    Route::post('/columns/{column}/cards', ColumnCardCreateController::class)
        ->name('columns.cards.store');

    Route::put('/columns/{column}/cards/{card}', ColumnCardUpdateController::class)
        ->scopeBindings()->name('columns.cards.update');
    Route::delete('/columns/{column}/cards/{card}', ColumnCardDestroyController::class)
        ->scopeBindings()->name('columns.cards.destroy');

    Route::put('/cards/reorder', CardsReorderUpdateController::class)
        ->name('cards.reorder');

    Route::post('/boards/{board}/category', CardCategoryCreateController::class)
        ->name('boards.category.store');
    Route::put('/boards/{board}/category/{cardCategory}', CardCategoryUpdateController::class)
        ->name('boards.category.update');
    Route::delete('/boards/{board}/category/{cardCategory}', CardCategoryDeleteController::class)
        ->name('boards.category.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
