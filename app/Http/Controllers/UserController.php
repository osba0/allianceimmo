<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Personnels;
use App\Models\Agence;
use App\Models\Filiale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Helpers\Helper;

class UserController extends Controller
{
    public function index()
    {
        $agences = Agence::get();
        $filiales = Filiale::get();
        return view('users/index', ["agences" => $agences, "filiales" => $filiales]);
    }

    public function list()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {

        $user = User::create([
            'name' => $request->pers_nom,
            'email' => $request->email,
            'username' => $request->nomUtilisateur,
            'password' => Hash::make($request->password),
            'agence_id' => $request->selectedAgenceId,
            'filiale_id' => $request->selectedFilialeId
        ]);

        $user->assignRole($request->role);

        $pers_id = Helper::IDGenerator(new Personnels, 'pers_id',config('constants.ID_LENGTH'), config('constants.PREFIX_PERS'));


        $personnel = new Personnels([
            'pers_id' => $pers_id,
            'pers_user_id' => $user->id,
            'pers_nom' => $request->pers_nom,
            'pers_prenom' => $request->pers_prenom,
            'pers_email' => $request->email,
            'pers_ind_1' => $request->pers_ind_1,
            'pers_tel_1' => $request->pers_tel_1,
            'pers_ind_2' => $request->pers_ind_2,
            'pers_tel_2' => $request->pers_tel_2,
            'pers_adress' => $request->pers_adress,
            'pers_ville' => $request->pers_ville,
            'pers_pays' => $request->pers_pays,
        ]);

        $user->personnel()->save($personnel);

        return response()->json($user, 201);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}

