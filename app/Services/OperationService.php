<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Operations;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helper;

class OperationService
{
    /**
     * Crée une opération de débit ou crédit.
     *
     * @param string $sens "debit" ou "credit"
     * @param string $type Type d'opération (ex: "bail", "charge")
     * @param float $montant Montant de l'opération
     * @param string|null $idBail Référence du bail
     * @param string|null $idCharge Référence de la charge
     * @param string $note Commentaire ou libellé
     * @param string|null $userId Utilisateur ayant effectué l'opération
     * @param array $options Données additionnelles (ex: type_autre, reserve_1...)
     *
     * @return Operation
     */
    public static function enregistrer(string $sens, string $type, float $montant, string $idBail = null, string $idCharge = null, string $note = '', string $userId = null, array $options = []): Operations
    {
        $operation = new Operations();

        $oper_id = Helper::IDGenerator(new Operations, 'oper_id',config('constants.ID_LENGTH'), config('constants.PREFIX_OPERATION'));
        $operation->oper_id = $oper_id; //'OPER-'.Str::upper(Str::random(8));
        $operation->oper_sens = strtolower($sens) === 'debit' ? 'DEBIT' : 'CREDIT';
        $operation->oper_type = $type;
        $operation->oper_type_autre = $options['type_autre'] ?? '';
        $operation->oper_montant = $montant;
        $operation->oper_id_bail = $idBail;
        $operation->oper_id_charge = $idCharge;
        $operation->oper_note = $note;
        $operation->oper_user = $userId ?? Auth::id();
        $operation->oper_reserve_1 = $options['reserve_1'] ?? null;
        $operation->oper_reserve_2 = $options['reserve_2'] ?? null;
        $operation->oper_reserve_3 = $options['reserve_3'] ?? null;

        $operation->save();

        return $operation;
    }

    /**
     * Calcule le solde global de l'agence (crédit - débit).
     *
     * @return float
     */
    public static function calculerSolde(): float
    {
        $totalCredit = Operations::where('oper_sens', 'credit')->sum('oper_montant');
        $totalDebit = Operations::where('oper_sens', 'debit')->sum('oper_montant');

        return $totalCredit - $totalDebit;
    }
}
