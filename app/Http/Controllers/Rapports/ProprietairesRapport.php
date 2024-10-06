<?php

namespace App\Http\Controllers\Rapports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Proprietaires;

class ProprietairesRapport extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Propriétaires
        $proprietaires = Proprietaires::paginate(6);

        return view('rapports/proprietaires/proprietaires', compact('proprietaires'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       // Trouver le propriétaire par son ID
        $proprietaire = Proprietaires::where("proprio_id", $id)->firstOrFail();
        return view('rapports/proprietaires/show', compact('proprietaire'));
    }
}
