@extends('layouts.app')

@section('content')

@php
    $title = "Rapports";
    $icon  = "fas fa-share-alt";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Rapports' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                     <Rapport-et-releve-loyer env="{{ env('APP_URL') }}"  :list-proprio="{{ json_encode($proprietaires) }}" :list-personnel="{{ json_encode($personnels) }}" :current-agence="{{ json_encode($agence) }}"  :list-locataire="{{ json_encode($listLocataire) }}" />
                   <!--div class="viewRapport d-flex justify-content-between">
                       <a href="{{ route('proprietairesRapport') }}" style="background: #219d21;">
                        <i class="fas fa-key"></i><br/>Propriétaires</a>
                       <a href="#" style="background: #4d81bb;">
                        <i class="fas fa-male"></i><br/>Locataires</a>
                       <a href="#" style="background: #cec47f;">
                        <i class="fas fa-home"></i><br/>Agence</a>
                   </div-->
                </div>
            </div>
        </div>
        <div class="col-md-12">

        </div>
    </div>
</div>
@endsection
