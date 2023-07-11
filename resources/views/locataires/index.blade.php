@extends('layouts.app')

@section('content')

@php
    $title = "Locataires";
    $icon  = "fas fa-male";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Locataires' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <Locataires></Locataires>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
