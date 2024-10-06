<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class GlobalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); //If user is not logged in then he can't access this page

    }

    public function getSolde()
    {
        // Calculer le solde total
        $credit = DB::table('operations')
            ->where('oper_sens', 'CREDIT') // Filtrer par type 'credit'
            ->sum('oper_montant'); // Somme des montants

        $debit = DB::table('operations')
            ->where('oper_sens', 'DEBIT') // Filtrer par type 'credit'
            ->sum('oper_montant');

        $soldeTotal = $credit - $debit;

        return response()->json([
            'codeRetour' => 0,
            'solde' => $soldeTotal
        ]);
    }
}
