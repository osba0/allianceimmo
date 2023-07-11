<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandatGerance extends Model
{

    use HasFactory;

    protected $table = 'mandat_gerances';
    protected $primaryKey = 'id';
    protected $fillable = [
        'mandat_id',
        'proprio',
        'bien',
        'agence',
        'pers',
        'mandat_duree',
        'mandat_date_debut',
        'mandat_date_fin',
        'mandat_user',
        'mandat_position',
        'mandat_fichiers',
        'mandat_preavis_mandataire',
        'mandat_preavis_proprio',
        'mandat_honoraire_gestion',
        'mandat_reserve_1',
        'mandat_reserve_2',
        'mandat_reserve_3',
        'mandat_reserve_4',
        'mandat_reserve_5',
    ];


    public function proprietaire()
    {
        return $this->belongsTo(Proprietaires::class, 'proprio_id'); 
    }


}
