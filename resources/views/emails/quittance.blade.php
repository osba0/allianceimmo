<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;

$json = json_decode($data, true);

$agence = Agence::where("id",$json['agence_id'])->first()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');


$moisLoyer = Carbon::createFromFormat('Y-m', $json['paiement_mois_location'])->format('M Y');

$locals = Local::leftJoin('biens', 'biens.bien_id', '=', 'locals.bien_id')
    ->select('locals.*', 'bien_nom', 'bien_adresse')
    ->whereIn("local_id", json_decode($json['bail_local']))
    ->groupBy('locals.local_id')
    ->get()->first()->toArray();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 15px; color: #333; }
    .header { background-color: #e9f1ff; padding: 10px; text-align: center; }
    .content { padding: 20px; }
    .footer { font-size: 13px; margin-top: 30px; color: #777; }
  </style>
</head>
<body>
  <div class="header">
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">Quittance de loyer</h2>
  </div>
  <div class="content" style="padding: 0 25px;">
    <p>Bonjour {{ $json['locat_prenom'] ?? '' }} {{ $json['locat_nom'] ?? '' }},</p>

    <p>Nous vous transmettons la <strong>quittance de loyer</strong> pour le mois de <strong>{{ $moisLoyer }}</strong>.</p>

    <p><strong>Détails :</strong></p>

    <ul>
        <li><strong>Référence :</strong> {{ $json['ref'] }}</li>
        <li><strong>Mois concerné :</strong> {{ $moisLoyer }}</li>
        <li><strong>Moyen de paiement :</strong> {{ $json['paiementType'] }}</li>
        <li><strong>Loyer mensuel :</strong> {{ number_format($json['paiement_montant'], 0, ',', ' ') }} FCFA</li>

        <li><strong>Local :</strong>  {{ $locals['local_type_local'] }}, {{ $locals['local_nature_local'] }} {{ $locals['local_type_location'] }}
                </li>
        <li><strong>Adresse :</strong> {{ $json['bien_adresse'] }}, {{ $json['bien_numero'] }} {{ $json['bien_ville'] }} ({{ $json['bien_pays'] }})</li>
    </ul>

    <p><a href="{{ env('APP_URL') }}{{ $json['fichier'] }}" style="display: inline-block;
      background-color: #0069d9;
      color: #ffffff;
      padding: 10px 18px;
      border-radius: 6px;
      font-weight: 500;
      font-family: Arial, sans-serif;
      text-decoration: none;
      transition: background-color 0.3s ease;">📄 Télécharger ici la quittance</a></p>

    <p>Nous restons à votre disposition pour toute information complémentaire.</p>

    <p>Cordialement,<br>
    <strong> L'équipe {{ $agence['agence_nom'] }} </strong></p>
  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
