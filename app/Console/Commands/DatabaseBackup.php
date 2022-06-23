<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'takes a backup from database';

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
        $filename = time() . ".sql";
  
        $command = 'mysqldump --user='.
        env('DB_USERNAME', 'root') .
        ' --password=' . env('DB_PASSWORD', '') .
        ' --host=' . env('DB_HOST', '127.0.0.1') .
        ' ' .
        env('DB_DATABASE', 'edu') .
        ' > "' . storage_path() . '\\app\\backups\\' . $filename . '"';

        system($command);

        $this->info('Backup Has Created Succesfully');
    }
}
