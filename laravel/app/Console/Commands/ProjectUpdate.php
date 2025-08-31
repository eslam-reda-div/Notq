<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Update';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('migrate');
        $this->call('filament:optimize-clear');
        $this->call('optimize:clear');
        $this->call('icon:cache');
        $this->call('l5-swagger:generate');
    }
}
