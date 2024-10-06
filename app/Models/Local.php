<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'local_id',
        'bien_id',
        'local_type_local',
        'local_type_location',
        'local_prix_loyer',
        'local_montant_charge',
        'local_superficie',
        'local_nombre_piece',
        'local_salle_bain',
        'local_description',
        'local_annee_construction',
        'local_disponible',
        'local_photos',
        'user'
    ];

    public function bien()
    {
        return $this->belongsTo(Bien::class, 'bien_id');
    }
}
