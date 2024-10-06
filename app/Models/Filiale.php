<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Filiale extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'agence_id',
        'filiale_name',
        'filiale_ind1',
        'filiale_tel1',
        'filiale_ind2',
        'filiale_tel2',
        'filiale_user',
        'filiale_logo',
        'filiale_ville',
        'filiale_pays'
    ];
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }
}
