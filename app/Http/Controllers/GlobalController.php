<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\OperationService;

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

        return response()->json([
            'codeRetour' => 0,
            'solde' => OperationService::calculerSolde()
        ]);
    }
}
