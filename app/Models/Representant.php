<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representant extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'repr_id',
        'repr_id_proprio',
        'repr_nom',
        'repr_civilite',
        'repr_prenom',
        'repr_tel_1',
        'repr_indicatif_1',
        'repr_type_piece',
        'repr_numero_piece',
        'repr_email',
        'repr_user'
    ];
}
