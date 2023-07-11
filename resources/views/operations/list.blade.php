@extends('layouts.app')

@section('content')

@php
    $title = "Liste des Opérations";
    $icon  = "fas fa-list";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Opérations' => ['url' => ''],
        'Liste des Opérations' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @php $etatPaiement = \App\Models\PaiementsLoyer::getEtatPaiement(); @endphp
                    <Operations-list :list-proprio="{{ json_encode($proprietaires) }}" :list-personnel="{{ json_encode($personnels) }}" :current-agence="{{ json_encode($agence) }}" :etat-paiement="{{ json_encode($etatPaiement) }}"></Operations-list>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
