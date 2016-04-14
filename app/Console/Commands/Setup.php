<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will setup the default application admin environment';

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
     * @return mixed
     */
    public function handle()
    {
        $path_to_sql = storage_path() . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'setup.sql';

        Artisan::call('migrate:refresh');
        $this->info('Migration successful');
        Artisan::call('db:seed');
        $this->info('DB seeder successful');

        $this->info('Path to sql file : '.$path_to_sql);
        exec('mysql -u ' . env('DB_USERNAME') . ' -p ' . env('DB_PASSWORD')
            . ' ' . env('DB_DATABASE') . ' < '.$path_to_sql);
        $this->info('Database default records successfully inserted');
    }
}
