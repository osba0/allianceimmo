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
                  <Paiement-loyers />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
