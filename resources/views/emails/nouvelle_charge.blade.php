<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Biens;
use App\Models\Agence;

$json = json_decode($data, true);

$agence = Agence::where("id", $json['agence_id'])->first()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

$bien = Biens::where("bien_id", $json['bien_id'])->firstOrFail()->toArray();

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
    <h2 style="background: rgb(38, 50, 77); color: white; text-align: center; padding: 4px 0">💸 Nouvelle charge enregistrée</h2>
  </div>
  <div style="padding: 0 25px;">


    <p>Bonjour {{ $json['proprio_prenom'] ?? '' }} {{ $json['proprio_nom'] ?? '' }},</p>


    <p>Nous vous informons qu'une nouvelle charge a été enregistrée sur votre bien {{ $bien['bien_nom'] }}, situé {{ $bien['bien_adresse'] }} {{ $bien['bien_numero'] }}, {{ $bien['bien_ville'] }} {{ $bien['bien_pays'] }}.à </p>

    <p>🧾 Détails de la charge :</p>

     <div class="info"><strong>Type charge:</strong> {{ ucfirst($json['type'] ?? 'autre') }}</div>
    <div class="info"><strong>Montant :</strong> {{ number_format($json['montant'] ?? 0, 0, ',', ' ') }} FCFA</div>
    <div class="info"><strong>Date :</strong> {{ \Carbon\Carbon::parse($json['date'] ?? now())->format('d/m/Y') }}</div>

    @if (!empty($json['note']))
        <div class="info"><strong>Description :</strong><br>{{ $json['note'] }}</div>
    @endif

    <p>Cette charge a été enregistrée par notre équipe de gestion, et le montant sera pris en compte dans le suivi de votre compte propriétaire.</p>

    <p>Si vous avez des questions ou souhaitez plus de détails, n'hésitez pas à nous contacter.</p>

    <strong>{{$agence['agence_nom']}}</strong></p>

  </div>
  <div class="footer" style="padding: 5px 25px; font-size: 13px; margin-top: 20px; color: #777; border: 1px solid #dedede; text-align: center;">
    Ceci est un message automatique. Merci de ne pas y répondre directement.
  </div>
</body>
</html>
