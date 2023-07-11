@extends('layouts.app')

@section('content')

@php
    $title = "Mandat de Gérance";
    $icon  = "fas fa-balance-scale";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Mandat de Gérance' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Mandat :list-proprio="{{ json_encode($proprietaires) }}" :list-personnel="{{ json_encode($personnels) }}" :current-agence="{{ json_encode($agence) }}"></Mandat>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
