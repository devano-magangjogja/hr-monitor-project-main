<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

class MarkTasksAsNotDone extends Command
{
    protected $signature   = 'tasks:mark-not-done';
    protected $description = 'Mark all pending tasks from yesterday as not done';

    public function __construct(
        protected TaskService $taskService
    ) {
        parent::__construct();
    }
    
    public function handle(): void
    {
        $this->info('Marking pending tasks as not done...');

        $count = $this->taskService->markAllPendingAsNotDone();

        $this->info("Done. {$count} task(s) marked as not done.");
    }
}