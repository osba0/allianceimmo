<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operations;
use App\Models\Proprietaires;
use App\Models\Locataires;
use App\Models\Biens;
use App\Models\Local;
use App\Http\Resources\OperationsResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index() {
        /*$lastOperations = Operations::leftJoin('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')->leftJoin('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')
        ->select('operations.*', 'bails.bail_locataire', 'bails.bail_local','bails.bail_proprio', 'charges_frais.charge_id_proprio', 'charges_frais.charge_id_bien', 'charges_frais.charge_id_local')->groupBy('operations.oper_id')->get();*

        $data = ['lastOperations' =>  OperationsResource::collection($lastOperations)];

        return view('dashboard/index', $data);*/
        $nbreProprietaires = Proprietaires::count();
        $nbreLocataires = Locataires::count();
        $nbreBiens = Biens::count();

         // Sum of 'prix_loyer' from the 'Local' table
        $revenuTotal = Local::sum('local_prix_loyer');

        // Count of available 'Local' records
        $biensDisponibles = Local::where('local_disponible', true)->count();

        $dernieresOperations = Operations::latest()->leftJoin('bails', 'operations.oper_id_bail', '=', 'bails.bail_id')->leftJoin('charges_frais', 'operations.oper_id_charge', '=', 'charges_frais.charge_id')
        ->select('operations.*', 'bails.bail_locataire', 'bails.bail_local','bails.bail_proprio', 'charges_frais.charge_id_proprio', 'charges_frais.charge_id_bien', 'charges_frais.charge_id_local')->groupBy('operations.oper_id')->take(5)->get();

        $data = [
            'nbreProprietaires' => $nbreProprietaires,
            'nbreLocataires'   => $nbreLocataires,
            'nbreBiens' => $nbreBiens,
            'dernieresOperations' => $dernieresOperations,
            'revenuTotal' => $revenuTotal,
            'biensDisponibles' => $biensDisponibles
        ];

        return view('dashboard/index', $data);
    }
    public function about() {
        return view('without');
    }

   public function getSoldeMensuel()
{
    // Définir la date de départ pour les 3 derniers mois
    $dateLimite = Carbon::now()->subMonths(3);

    // Créer une liste des mois pour les 3 derniers mois dans l'ordre décroissant
    $monthsList = [];
    for ($i = 2; $i >= 0; $i--) { // Modifié pour commencer par le mois le plus ancien
        $monthsList[] = Carbon::now()->subMonths($i)->format('F Y');
    }

    // Récupérer les données groupées par mois
    $soldeMensuel = Operations::select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('YEAR(created_at) as year'),
        DB::raw('SUM(CASE WHEN oper_sens = "CREDIT" THEN oper_montant ELSE 0 END) as total_credits'),
        DB::raw('SUM(CASE WHEN oper_sens = "DEBIT" THEN oper_montant ELSE 0 END) as total_debits')
    )
    ->where('created_at', '>=', $dateLimite)
    ->groupBy('year', 'month')
    ->orderBy('year', 'asc') // Trie par année
    ->orderBy('month', 'asc') // Trie par mois
    ->get();

    // Préparer les données pour le graphique
    $months = [];
    $credits = [];
    $debits = [];
    $solde = []; // Nouveau tableau pour stocker le solde

    // Initialiser les mois avec 0 pour les crédits, débits, et soldes
    foreach ($monthsList as $monthName) {
        $months[] = $monthName;
        $credits[] = 0;
        $debits[] = 0;
        $solde[] = 0; // Initialiser chaque mois avec un solde de 0
    }

    // Remplir les données récupérées
    foreach ($soldeMensuel as $item) {
        $monthIndex = array_search(Carbon::createFromFormat('m', $item->month)->format('F Y'), $months);

        if ($monthIndex !== false) {
            $credits[$monthIndex] = $item->total_credits;
            $debits[$monthIndex] = $item->total_debits;
            // Calculer le solde pour chaque mois (Crédit - Débit)
            $solde[$monthIndex] = $item->total_credits - $item->total_debits;
        }
    }

    //return response() -> json(["months"=> ["August 2024","September 2024","October 2024"],"credits"=> [0,100000,210000],"debits"=> [0,25000,55000],"solde"=> [0,50000,155000]]);

    return response()->json([
        'months' => $months,
        'credits' => $credits,
        'debits' => $debits,
        'solde' => $solde, // Ajouter les soldes au retour de la réponse
    ]);
}
}
