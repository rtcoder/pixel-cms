<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class setClientSecret extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-passport-client-secret {new_secret?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set client secret do constant value from front application';

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
        $data = DB::table('oauth_clients')->where('id', 2)->get();
        if (!count($data)) {
            return;
        }
        $new_secret = $this->argument('new_secret') ?: 'ic6LIFYbZah73CnjriSsQmdu0TMoSybm09lsGd9T';
        DB::table('oauth_clients')->where('id', 2)->update(['secret' => $new_secret]);
    }
}
