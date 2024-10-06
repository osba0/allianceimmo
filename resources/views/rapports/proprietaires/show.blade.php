@extends('layouts.app')

@section('content')

@php
    $title = "Rapports ";
    $icon  = "fas fa-key";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Rapports' => ['url' => '/rapport/index'],
        'Propriétaires' => ['url' => '/rapport/proprietaires'],
        $proprietaire->proprio_prenom.' '.$proprietaire->proprio_nom => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
       A venir
   </div>
</div>
@endsection
