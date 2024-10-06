@extends('layouts.app')

@section('content')

@php
    $title = "Rapports Propriétaires";
    $icon  = "fas fa-key";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Rapports' => ['url' => '/rapport/index'],
        'Propriétaires' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        @foreach($proprietaires as $proprietaire)
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><strong>{{ $proprietaire->proprio_prenom }} {{ $proprietaire->proprio_nom }}</strong></h5>
                </div>
                <div class="card-body">
                    <p><strong>Email :</strong> {{ $proprietaire->proprio_email }}</p>
                    <p class="mb-0"><strong>Téléphone :</strong> {{ $proprietaire->proprio_indicatif_1 }} {{ $proprietaire->proprio_tel_1 }}</p>
                    <!-- Autres détails si nécessaire -->
                </div>
                <div class="card-footer">
                    <a href="{{ route('proprietaires.show', $proprietaire->proprio_id) }}" class="btn btn-primary">Voir les Rapports</a>
                </div>
            </div>
        </div>
        @endforeach


   </div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-center">
                {{ $proprietaires->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
