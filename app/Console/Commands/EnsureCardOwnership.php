<?php

namespace App\Console\Commands;

use App\Models\Scopes\UserScope;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureCardOwnership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-card-ownership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = User::query();

        // progress bar
        $bar = $this->output->createProgressBar($query->count());
        
        // update each user's cards to have the user_id
        $query->chunk(100, function ($users) use ($bar) {
            foreach ($users as $user) {
                $boards = $user->boards() -> withoutGlobalScope(UserScope::class) -> get();
                // iterte over each board 
                foreach ($boards as $board) {
                    $columns = $board->columns() -> withoutGlobalScope(UserScope::class) -> get();
                    // iterate over each column
                    foreach ($columns as $column) {
                        // updating column with title 
                        // $this -> info('Updatingcards for column with title' . $column -> title);
                        $column -> cards() -> withoutGlobalScope(UserScope::class) -> update(['user_id' => $user -> id]);
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->info(PHP_EOL. 'All cards have been updated with the correct user_id');
    }
}
