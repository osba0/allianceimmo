<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RapportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get 
        $data = [];

        return view('rapports/index', $data);
    }
}
