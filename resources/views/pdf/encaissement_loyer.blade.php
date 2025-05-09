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
                                    <h6 style="margin: 0; font-size: 14px;">PROPRIÉTAIRE</h6>
                                    <h2 style="margin: 0; font-size: 20px;">{{ $meta['proprietaire'] }}</h2>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 5px;">
                                    <strong>Tél :</strong> +{{ $meta['proprietaire_indicatif'] }}{{ $meta['proprietaire_telephone'] }}<br>
                                    <strong>Adresse :</strong> {{ $meta['proprietaire_adresse'] }}, {{ $meta['proprietaire_ville'] }} ({{ $meta['proprietaire_pays'] }})
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
        <strong>DETAILS ENCAISSEMENTS LOYERS PERIODE : {{ $formatDateDebut }} au  {{ $formatDateFin }}</strong>
    </div>

    <table class="table2">
        <thead>
            <tr>
                <th style="width: 20%; font-size: 10px">LOCATAIRE</th>
                <th style="width: 20%; font-size: 10px">LOCAL LOUE</th>
                <th style="width: 20%; font-size: 10px">ADRESSE</th>
                <th style="width: 20%; font-size: 10px">MONTANT LOYER</th>
                <th style="width: 20%; font-size: 10px">LOYERS ENCAISSES</th>
            </tr>
        </thead>
        <tbody>
            @php $total=0; @endphp
            @foreach ($rapports as $item)
                @php
                    $total+=$item->montant_total;
                    $mandat_honoraire_gestion = floatval($item->mandat_honoraire_gestion);
                @endphp
                <tr>
                    <td>{{ $item->locat_nom }} {{ $item->locat_prenom }}</td>
                    <td>{{ $item->local_type_local }} ({{$item->local_nature_local}})</td>
                    <td>{{ $item->bien_adresse }}</td>
                    <td style="text-align: right;">{{ number_format($item->bail_montant_ht, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right;">{{ number_format($item->montant_total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
            @php
            $montantNetEncaisse = $total - $total * ($mandat_honoraire_gestion/100);
            @endphp

            <tr>
                <td colspan="4" style="background: #ffffff;">
                    <strong>TOTAL LOYERS ENCAISSES</strong>
                </td>
                <td style="text-align: right;">
                    <strong>{{ number_format($total, 0, ',', ' ') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="background: #ffffff;">
                    <strong>TOTAL COMMISSION ({{ strval($mandat_honoraire_gestion)}}%)</strong>
                </td>
                <td style="text-align: right;">
                    <strong>{{ number_format($total * ($mandat_honoraire_gestion/100), 0, ',', ' ') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="background: #ffffff;">
                    <strong>TOTAL LOYER NET PERCU PAR LE PROPRIETAIRE DU {{ $formatDateDebut }} au  {{ $formatDateFin }} </strong>
                </td>
                <td style="text-align: right;">
                    <strong>{{ number_format($montantNetEncaisse, 0, ',', ' ') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="background: #49749e; text-align: center; padding: 8px 0; color: #ffffff">
        <strong>RELEVE DE COMPTE PERIODE : {{ $formatDateDebut }} au  {{ $formatDateFin }}</strong>
    </div>
    <table class="table2" style="font-size: 16px">
        <thead>
            <tr>
                <th style="width: 20%; font-size: 10px">DATE</th>
                <th style="width: 20%; font-size: 10px">DESCRIPTION</th>
                <th style="width: 20%; font-size: 10px; color: green; text-align: center">ENTREE</th>
                <th style="width: 20%; font-size: 10px; color: red; text-align: center">SORTIE</th>
                <th style="width: 20%; font-size: 10px">SOLDE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="8%">{{ $formatDateDebut }}</td>
                <td width="62%">Solde antérieur</td>
                <td width="10%" style="text-align: right; ">{{ number_format($meta['soldeAnterieur'], 0, ',', ' ')  }}</td>
                <td width="10%"></td>
                <td width="10%" style="text-align: right;">{{ number_format($meta['soldeAnterieur'], 0, ',', ' ')  }}</td>
            </tr>
            @php $totalSortie=0; @endphp
            @foreach ($debits as $item)
                @php
                    $totalSortie+=$item->charge_montant;
                    $resteMntSolde = $meta['soldeAnterieur'] - $item->charge_montant;

                @endphp
                <tr>
                    <td>{{ Carbon::parse($item->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $item->charge_note }}</td>
                    <td></td>
                    <td style="text-align: right;">{{ number_format($item->charge_montant, 0, ',', ' ') }}</td>
                    <td style="text-align: right;">{{ number_format($resteMntSolde, 0, ',', ' ') }}</td>
                </tr>
            @endforeach

            <tr>
                <td>{{ $formatDateFin }}</td>
                <td>Loyer net peçu du {{ $formatDateDebut }} au  {{ $formatDateFin }}</td>
                <td style="text-align: right;">{{ number_format($montantNetEncaisse, 0, ',', ' ') }}</td>
                <td style="text-align: right;"></td>
                <td style="text-align: right;">{{ number_format($montantNetEncaisse+$resteMntSolde, 0, ',', ' ') }}</td>
            </tr>

            <tr>
                <td colspan="2" style="background: #ffffff;">
                    <strong>TOTAL DES MOUVEMENTS</strong>
                </td>
                <td style="text-align: right;">
                    <strong>{{ number_format($montantNetEncaisse+$meta['soldeAnterieur'], 0, ',', ' ') }}</strong>
                </td>
                 <td style="text-align: right;">
                    <strong>{{ number_format($totalSortie, 0, ',', ' ') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="background: #ffffff;">
                    <strong>SOLDE FINAL A LA DATE DU {{ $formatDateFin }}</strong>
                </td>
                <td style="text-align: right;">
                    <strong>{{ number_format($montantNetEncaisse+$resteMntSolde, 0, ',', ' ') }}</strong>
                </td>
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
