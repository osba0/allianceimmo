<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use DB;

class VerifierMandatGerance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verifier-expiration-mandat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $today = Carbon::now()->toDateString();

        $mandatsExpirés = DB::table('mandat_gerances')
            ->where('mandat_etat', 1)
            ->whereDate('mandat_date_fin', '<', $today)
            ->update(['mandat_etat' => 0]);

        $this->info("Mandats désactivés : $mandatsExpirés");
    }
}
