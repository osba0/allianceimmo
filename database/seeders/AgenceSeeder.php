<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('agences')->insert([
            [
                'agence_id' => 'AG-000000',
                'agence_nom' => 'ALLIANCE BAZICS IMMO',
                'agence_slogan' => 'Votre partenaire de confiance',
                'agence_activite' => 'Consulting',
                'agence_ninea' => '123456789',
                'agence_email' => 'bazics@yopmail.com',
                'agence_ind1' => '+221',
                'agence_tel1' => '33 123 45 67',
                'agence_ind2' => '+221',
                'agence_tel2' => '77 123 45 67',
                'agence_adresse' => '123 Rue de Dakar',
                'agence_ville' => 'Dakar',
                'agence_pays' => 'Sénégal',
                'agence_logo' => 'logo.png',
                'agence_user' => 'osba',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
