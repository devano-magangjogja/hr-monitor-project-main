<?php

namespace App\Console\Commands;

use App\Services\DefaultTaskService;
use Illuminate\Console\Command;

class GenerateDailyDefaultTasks extends Command
{
    protected $signature   = 'tasks:generate-daily';
    protected $description = 'Generate daily task instances from active default tasks';

    public function __construct(
        protected DefaultTaskService $defaultTaskService
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Generating daily default tasks...');

        $this->defaultTaskService->generateDailyTasks();

        $this->info('Done. Check logs for details.');
    }
}