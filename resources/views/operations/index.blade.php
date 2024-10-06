@extends('layouts.app')

@section('content')

@php
    $title = "Loyers & Paiement";
    $icon  = "fas fa-random";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Opérations' => ['url' => ''],
        'Loyers & Paiement' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @php $etatPaiement = \App\Models\PaiementsLoyer::getEtatPaiement(); @endphp
                    <Operations :list-proprio="{{ json_encode($proprietaires) }}" :list-personnel="{{ json_encode($personnels) }}" :current-agence="{{ json_encode($agence) }}" :etat-paiement="{{ json_encode($etatPaiement) }}" :list-locataire="{{ json_encode($listLocataire) }}"></Operations>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
