@extends('layouts.app')

@section('content')

@php
    $title = "Agence";
    $icon  = "fas fa-home";
    $breadcrumbs = [
        'Tableau de bord' => ['url' => '/home'],
        'Agence' => ['url' => '']
    ];
@endphp

<div class="container-fluide">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <agence-list :list-agences="{{ json_encode($agences) }}"></agence-list>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
