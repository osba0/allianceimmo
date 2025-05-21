<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TachesAutomatisesController extends Controller
{
    // index
     public function index()
    {
        return view('taches/index');
    }

    public function runCommand()
    {

        // Exécution de la commande
        Artisan::call(request('command'));


        $output = Artisan::output();

        return response()->json([
            'message' => 'Commande exécutée avec succès ✅',
            'output' => $output
        ]);
    }
}
