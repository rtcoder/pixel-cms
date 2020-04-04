<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PassportOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup-passport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup passport required things';

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
     * @return void
     */
    public function handle()
    {
        if (!Schema::hasTable('oauth_clients') || DB::table('oauth_clients')->where('id', 2)->get()) {
            Artisan::call('passport:install');
        }
        Artisan::call('passport:keys');
        Artisan::call('update-passport-client-secret');
    }
}
