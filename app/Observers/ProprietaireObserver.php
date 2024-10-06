<?php

namespace App\Observers;

use App\Models\Proprietaires;
use App\Models\Biens;
use App\Models\Local;
use App\Models\MandatGerance;

class ProprietaireObserver
{
    /**
     * Handle the Proprietaire "deleted" event.
     *
     * @param  \App\Models\Proprietaire  $proprietaire
     * @return void
     */
    public function deleted(Proprietaires $proprietaire)
    {
        // Supprimer les biens et les locaux associés
        $proprietaire->biens->each(function ($bien) {
            // Supprimer les locals liés au bien
            Local::where('bien_id', $bien->id)->delete();
            // Supprimer le bien
            $bien->delete();
        });

    }

}
