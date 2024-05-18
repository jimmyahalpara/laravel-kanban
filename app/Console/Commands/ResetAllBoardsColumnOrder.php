<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Scopes\UserScope;

class ResetAllBoardsColumnOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-all-boards-column-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all boards column order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting all boards column order...');

        $boards = \App\Models\Board::withoutGlobalScope(UserScope::class)->get();
        $boards->each(function ($board) {
            $board->resetColumnOrder();
        });

        $this->info('All boards column order has been reset.');
    }
}
