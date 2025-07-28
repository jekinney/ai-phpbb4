<?php

namespace App\Console\Commands;

use App\Jobs\ResetGameLeaderboards;
use Illuminate\Console\Command;

class ScheduleGameResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:schedule-resets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule game leaderboard resets based on game reset frequency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching game leaderboard reset job...');
        
        ResetGameLeaderboards::dispatch();
        
        $this->info('Game leaderboard reset job has been dispatched successfully.');
        
        return 0;
    }
}
