<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\EtatPaiement;
use App\Models\Bail;
use App\Models\PaiementsLoyer;

use App\Helpers\Helper;

use Carbon\Carbon;
use DB;

class GenerationLoyer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:loyers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer les paiements des loyers à chaque fin du mois';

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
        // Lister les loyers des locataires qui ont un bail en cours
        $mois_paiement = Carbon::yesterday()->format('Y-m');

        $toDate = Carbon::parse($mois_paiement);

        

        //$bails_en_cours = Bail::leftJoin('paiement_location', 'bails.bail_id', '=', 'paiement_location.paiement_bail_id')->select("bails.bail_date_debut, bails.bail_montant_ht")->where("bail_etat", true)->groupBy('bails.bail_id')->get();

        $bails_en_cours = Bail::where("bail_etat", true)->get()->toArray();


        foreach($bails_en_cours as $bail){
            $fromDate = Carbon::parse($bail["bail_date_debut"]);
            $fromDateFormat = $fromDate->format('Y-m');
            // Nbre de mois 
            $months = $toDate->diffInMonths($fromDateFormat);
            if($months > 0){
                $i=0;
                while($i<$months){

                    if(!$this->checkMonthExist($bail["bail_id"], $fromDateFormat)){
                        // inserer un nouveau loyer
                        $paiement_id = Helper::IDGenerator(new PaiementsLoyer, 'paiement_id',config('constants.ID_LENGTH'), config('constants.PREFIX_PAIEMENT_LOYER'));
                        DB::insert('insert into paiements_loyers (paiement_id, paiement_bail_id, paiement_montant, paiement_mois_location, paiement_echeance, paiement_etat, paiement_cloture, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', [$paiement_id, $bail["bail_id"], $bail["bail_montant_ht"],$fromDateFormat, '', false, false, Carbon::now(), Carbon::now()]);
                    }
                    $fromDate = $fromDate->addMonth();
                    $fromDateFormat = $fromDate->format('Y-m');
                    $i++;
                }

            }
            print_r($months);

        }

        //var_dump($bails_en_cours);
    }

    public function checkMonthExist($bail, $month){
        $is_exist = DB::table('paiements_loyers')->where('paiement_bail_id', $bail)->where('paiement_mois_location', $month)->first();
        if($is_exist){
            return true;
        }else{
            return false;
        }
    }
}
