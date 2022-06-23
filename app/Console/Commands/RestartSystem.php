<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class RestartSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart System To Its Defaults';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!defined('STDIN')) {
          define('STDIN', fopen('php://stdin', 'r'));
        }

        $allFiles = new Filesystem;
        $allFiles->cleanDirectory('/storage/app/images/teachers');
        $allFiles->cleanDirectory('/storage/app/images/users');
        $allFiles->cleanDirectory('/storage/app/backups');

        Artisan::call('route:cache');
        Artisan::call('config:cache');
        Artisan::call('view:cache');
        Artisan::call('event:cache');

        Artisan::call('migrate:fresh');

        return $this->info('Application Has Restarted Succesfully');
    }
}
