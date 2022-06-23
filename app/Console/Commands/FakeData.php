<?php

namespace App\Console\Commands;

use App\Models\Admin\Student;
use App\Models\Admin\Teacher;
use App\Models\Admin\Lesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FakeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare Fake Data For Testing';

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
        Artisan::call('migrate:fresh --seed');
        // Student::factory()->count(500)->create();
        // Teacher::factory()->count(500)->create();
        // Lesson::factory()->count(50)->create();

        return $this->info('Data Has Created Succesfully');
    }
}
