<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargesFrais extends Model
{
    use HasFactory;

    protected $casts = [
        'charge_facture' => 'array'
    ];

    protected $primaryKey = 'id';
    protected $fillable = [
        'charge_id',
        'charge_type',
        'charge_type_autre',
        'charge_montant',
        'charge_id_proprio',
        'charge_id_bien',
        'charge_id_local',
        'charge_note',
        'charge_user',
        'charge_facture',
        'charge_reserve_1',
        'charge_reserve_2',
        'charge_reserve_3'
    ];
}
