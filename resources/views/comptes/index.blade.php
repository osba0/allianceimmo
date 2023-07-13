@extends('layouts.app')

@section('content')

@php
    $title = "Comptes";
    $icon  = "fas fa-address-card";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Comptes' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                   A venir
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
