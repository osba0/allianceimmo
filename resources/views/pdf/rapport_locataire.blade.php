<?php
/**
 * @var array $data
 */
//$mainLogo = Settings::getValue('adminSettings:mainLogo');
use Carbon\Carbon;
use App\Models\Local;
use App\Models\Agence;

$agence = Agence::where("id",auth()->user()->agence_id)->first()->toArray();

$logoPath = public_path('assets/images/logo_ab_immo_rounded_1.png');

$formatDateDebut = Carbon::createFromFormat('Y-m-d', $meta['debut'])
        ->startOfMonth()
        ->format('d/m/Y');
$formatDateFin = Carbon::createFromFormat('Y-m-d', $meta['fin'])
        ->format('d/m/Y');


function getTotalPaiement($paiementRecu) {
    $total = 0;

    if (!empty($paiementRecu)) {
        try {
            $paiements = is_string($paiementRecu) ? json_decode($paiementRecu, true) : $paiementRecu;

            if (is_array($paiements)) {
                foreach ($paiements as $paiement) {
                    $montant = isset($paiement['paiementMontant']) ? floatval($paiement['paiementMontant']) : 0;
                    $total += $montant;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Erreur dans getTotalPaiement: ' . $e->getMessage());
        }
    }

    return $total;
}

function getStatutPaiement($etatPaiement) {
    switch ($etatPaiement) {
        case 3:
            return 'Payé';
        case 2:
            return 'Partiel';
        case 0:
            return 'Impayé';
        default:
            return (string) $etatPaiement . ' inconnu';
    }
}

?>

<style>
    .main {
        padding: 0;
        margin: 0;
        width: 21cm;
        min-height: 100%;
        box-sizing: border-box;
        /* Arrière-plan avec l'image centrée et semi-transparente */
        /*background-image: url("https://allianceimmo.net/assets/images/logo-login.png");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 50%;
        opacity: 0.1;*/
    }

     /* Logo en arrière-plan, centré et fixe sur chaque page */
    .background-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 50%; /* Ajustez la taille de l'image ici */
        opacity: 0.1; /* Transparence pour effet filigrane */
        transform: translate(-50%, -50%); /* Centre l'image */
        z-index: -1; /* Place derrière le contenu principal */
    }
    .numero{
        border: 1px solid #000;
        width: 50%;
        margin-top: 10px;
    }

    table {
        width: 100%;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        padding: 8px 5px;
        border: 1px solid #000000;
        border-right: none;
    }
    .table .border-right {
        border-right: 1px solid #000000;
    }
    .table td {
        border-top: none;
    }

    .logo {
        max-width: 100%;
    }
    .infoAgence{

    }
    ul{
        list-style: none;
    }
    .table-footer{
        border-top: 1px solid #000;
    }

    .table2 {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .table2 th, .table2 td {
        border: 1px solid #444;
        padding: 8px;
        text-align: left;
    }

    .table2 th {
        background-color: #f2f2f2;
    }

    .table2 tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    @page {
        margin-top: 4cm;
        margin-bottom: 2cm;
        margin-left: 1cm;
        margin-right: 1cm;
        header: page-header;
        footer: page-footer;
    }
</style>

<body style="font-family: helvetica; font-size: 12px;">
     <!--img src="https://allianceimmo.net/assets/images/logo-login.png" class="background-logo" alt="Background Logo"-->
<div class="main">

    {{-- HEAD --}}
    <htmlpageheader class="header" name="page-header">
        <div class="top-wrapper">
            <table border="0" cellpadding="0" cellspacing="0" style="width:100%; padding: 0 0 5px 0;">
                <tbody>
                <tr>
                    <td style="text-align:left; width: 45%">
                        <table class="infoAgence" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    @if(file_exists($logoPath))
                                        <img src="assets/images/logo_ab_immo_rounded_1.png" class="logo" alt="Logo">
                                    @else
                                        <img src="https://allianceimmo.net/assets/images/logo-login.png" class="logo" alt="Default Logo"/>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $agence['agence_nom'] }}</strong><br/>
                                    {{ $agence['agence_slogan'] }}<br/>
                                    {{ $agence['agence_adresse'] }}<br/>
                                    Tel: {{ $agence['agence_tel1'] }} ou {{ $agence['agence_tel2'] }}<br/>
                                    {{ $agence['agence_email'] }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 10%"></td>
                    <td style="width: 45%; text-align:center;background: #ddd;">
                       <table width="90%" style="border-collapse: collapse; margin-bottom: 0;">
                            <tr>
                                <td colspan="2" style="border-top: 0px solid #000; border-bottom: 0px solid #000; padding: 5px 0 5px 0;">
                                    <h6 style="margin: 0; font-size: 14px;">LOCATAIRE</h6>
                                    <h2 style="margin: 0; font-size: 20px;">{{ $meta['locataire'] }}</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 5px;">
                                    <strong>Tél :</strong> +{{ $meta['locataire_indicatif'] }}{{ $meta['locataire_telephone'] }}<br>
                                    <strong>Adresse :</strong> {{ $meta['locataire_adresse'] }}, {{ $meta['locataire_ville'] }} ({{ $meta['locataire_pays'] }})
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="line"></div>

        </div>
    </htmlpageheader>
    {{-- /HEAD --}}
    <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 0;">
        <tr><td height="40"></td></tr>
     </table>
    <div style="background: #d6c689; text-align: center; padding: 8px 0;">
        <strong>DETAILS LOYERS PERIODE : {{ $formatDateDebut }} au  {{ $formatDateFin }}</strong>
    </div>

    <table class="table2">
        <thead>
            <tr>
                <th>LOCAL LOUE</th>
                <th>MOIS</th>
                <th style="text-align: right;">MONTANT LOYER</th>
                <th style="text-transform: uppercase;">Total payé</th>
                <th style="text-transform: uppercase; text-align: center">Statut</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($rapports as $item)

                <tr>
                    <td>{{ $item->local_type_local }} ({{$item->local_nature_local}})</td>
                    <td>{{ $item->paiement_mois_location }}</td>
                    <td>{{ $item->paiement_montant }}</td>
                    <td style="text-align: right;">{{ number_format(getTotalPaiement($item->paiement_recu), 0, ',', ' ') }}</td>
                    <td style="text-align: center;"><span style="color: {{ $item->paiement_etat == 3 ? 'green' : ($item->paiement_etat == 2 ? 'orange' : 'red') }}">{{ getStatutPaiement($item->paiement_etat) }}</span></td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right; font-size: 16px;">Montant total payé</td>
                <td style="text-align: right;  font-size: 16px; color:#28a745"><strong>{{ number_format($meta['total_paye'], 0, ',', ' ')  }}</strong></td>

            </tr>
             <tr>
                <td colspan="3" style="text-align: right;  font-size: 16px;">Montant impayé</td>
                <td style="text-align: right;  font-size: 16px; color:#dc3545"><strong>{{ number_format($meta['total_impayes'], 0, ',', ' ') }}</strong></td>

            </tr>

        </tbody>
    </table>
    <table>
        <tbody>
            <tr>
                <td align="left" colspan="2">{{ $agence['agence_ville'] }}, le {{ now()->format('d/m/Y H:i:s') }}</td>
                <td>Le Gérant: {{ $agence['agence_gerant'] }}</td>
                <td align="right">{!! generateQrCode($meta['pathFile'], 100) !!}</td>

            </tr>
        </tbody>
    </table>


    {{-- FOOTER --}}
    <htmlpagefooter class="footer" name="page-footer">
         <table class="table-footer" cellspacing="0" cellpadding="0">
            <tbody>
                <tr><td colspan="2" height="8"></td></tr>
                <tr>
                    <td align="left" width="80%">
                        <strong>{{ $agence['agence_nom'] }}</strong>
                        {{ $agence['agence_ville'] }} ({{ $agence['agence_pays'] }}), {{ $agence['agence_adresse'] }}, tél:{{ $agence['agence_ind1'] }} {{ $agence['agence_tel1'] }} Ninea: {{ $agence['agence_ninea'] }}
                    </td>
                    <td align="right"> Page {PAGENO} / {nbpg}</td>
                </tr>
            </tbody>
        </table>
    </htmlpagefooter>
     {{-- /FOOTER --}}
</div>
</body>
