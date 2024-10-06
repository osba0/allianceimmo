<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Personnels;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Helpers\Helper;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Créer un utilisateur
        $user = User::create([
            'name' => 'Oumar',
            'email' => 'osba@yopmail.com',
            'username' => 'osba',
            'password' => bcrypt('passer@123'), // Changez le mot de passe selon vos besoins
            'agence_id' => 1,
        ]);

        // Assigner le rôle Root à l'utilisateur
        $role = Role::where('name', 'root')->first();
        $user->assignRole($role);

        // Créer une entrée dans la table personnels pour cet utilisateur
        $pers_id = Helper::IDGenerator(new Personnels, 'pers_id',config('constants.ID_LENGTH'), config('constants.PREFIX_PERS'));
        Personnels::create([
            'pers_id' => $pers_id,
            'pers_user_id' => $user->id,
            'pers_nom' => 'Oumar Samba',
            'pers_prenom' => 'User',
            'pers_email' => 'osba@yopmail.com',
            'pers_ind_1' => '221',
            'pers_tel_1' => '775670362',
            'pers_ind_2' => null,
            'pers_tel_2' => null,
            'pers_adress' => 'PA U21 numer 002',
            'pers_ville' => 'Dakar',
            'pers_pays' => 'SENEGAL',
            'user' => 'root',
        ]);
    }
}
