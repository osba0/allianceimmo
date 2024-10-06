<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Agence;

use App\Http\Resources\AgenceResource;

use DB;
use File;

class AgenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get Agence
        $listAgences = Agence::get();

        $data = ['agences' => $listAgences];

        return view('agence/index', $data);
    }

    public function listing()
    {
        $user = Auth::user();

        $paginate = request('paginate');

        $agences = DB::table('agences')->leftJoin('filiales', 'agences.id', '=', 'filiales.agence_id')->select('agences.*', DB::raw('COUNT(filiales.agence_id) as totalFialiale'));

        if(isset($paginate)){
            $agences = $agences->groupBy("agences.agence_id")->orderby("created_at", "desc")->paginate($paginate);
        }else{
            $agences = $agences->get();
        }


        return AgenceResource::collection($agences);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
