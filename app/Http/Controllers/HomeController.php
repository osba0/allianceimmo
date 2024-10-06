<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Operations;
use App\Models\Proprietaires;
use App\Models\Locataires;
use App\Models\Biens;
use App\Models\Local;
use App\Http\Resources\OperationsResource;

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
}
