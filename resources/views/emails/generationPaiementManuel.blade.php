<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;

$json = json_decode($data, true);

$details = json_decode($json['details'], true);

//var_dump($details["local_type_location"]); die();


$agence = Agence::where("id", $json['agence_id'])->first()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

$moisLoyer = Carbon::createFromFormat('Y-m', $json['paiement_mois_location'])->format('M Y');

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 15px; color: #333; }
    .header { background-color: #000; color: white; padding: 10px; text-align: center; }
    .content { padding: 25px; }
    .footer { font-size: 13px; margin-top: 30px; color: #777; }
  </style>
</head>
<body>

  <div class="header">
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">🔔 Paiement manuel initié</h2>
  </div>
  <div style="padding: 0 25px;">

   
    <p>Une nouvelle opération a été enregistrée dans {{ $agence['agence_nom'] }}</p>

    <div class="details">
      <p><strong>Réf :</strong> {{$json['ref']}}</p>
      <p><strong>Montant :</strong> {{ number_format($json['montantLoyer'], 0, ',', ' ') }} FCFA</p>
      <p><strong>Mois :</strong> {{$moisLoyer}}</p>
      <p><strong>Type local :</strong> {{ $details['local_type_local']}}</p>
      <p><strong>Type location :</strong> {{ $details['local_type_location']}}</p>
      <p><strong>Adresse :</strong> {{ $details['bien_numero'] }} {{ $details['bien_adresse'] }}, {{ $details['bien_ville'] }} ({{ $details['bien_pays'] }})</p>
      <p><strong>Date :</strong> {{ $json['date'] }}</p>
      <p><strong>Initié par :</strong> {{ $json['nomAgent']}} ({{$json['agent']}})</p>
    </div>

  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
